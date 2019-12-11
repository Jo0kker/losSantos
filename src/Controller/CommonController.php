<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(NewsRepository $repo)
    {
        $news = $repo->findBy([], [
            'id' => 'DESC'
        ]);
        return $this->render('common/index.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * Permet de creer une annonce
     * @Route("/newNews", name="new_News")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function newNews(Request $request, EntityManagerInterface $manager)
    {
        $news = new News();
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $img = $form['img']->getData();
            $tag = $_POST['tag'];
            if (!in_array($tag, $user->getRoles(), true)) {
                $this->addFlash(
                    'danger',
                    'Vous ne pouvez pas utiliser ce tag'
                );
                return $this->redirectToRoute('new_News');
            }
            if ($img) {
                $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$img->guessExtension();
                try {
                    $img->move($this->getParameter('news_directory'), $newFilename);
                } catch (FileException $e) {

                }
                $chemin = 'uploads/news/' . $newFilename;
                $news->setImg($chemin);
                $news->setTags($tag);
                $manager->persist($news);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Annonce enregistrer'
                );
                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render('common/newNews.html.twig', [
            'form' => $form->createView(),
            'roles' => $userRoles
        ]);
    }

    /**
     * Permet d'afficher le detail de la news
     * @Route("/news/{slug}", name="show_news")
     * @param News $news
     * @return Response
     */
    public function show_news(News $news): Response
    {
        return $this->render('common/news.html.twig', [
            'news' => $news
        ]);
    }
}
