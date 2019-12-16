<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Docs;
use App\Entity\News;
use App\Form\ContactType;
use App\Form\DocsType;
use App\Form\NewsType;
use App\Repository\DocsRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{

    /** PARTIE NEWS */

    /**
     * Page d'acceuil affiche les news
     * @Route("/", name="homepage")
     */
    public function index(NewsRepository $repo)
    {
        if (isset($_GET['tag'])) {
            $tag = htmlspecialchars(trim($_GET['tag']));
            $news = $repo->findBy([
                'tags' => $tag
            ],[
                'createdAt' => 'DESC'
            ]);
        }else{
            $news = $repo->findBy([], [
                'id' => 'DESC'
            ]);
        }
        return $this->render('common/index.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * Permet de creer une annonce
     * @Route("/newNews", name="new_News")
     * @IsGranted("ROLE_Lspd_CHIEF")
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


    /** PARTIE DOCUMENTS  */



    /**
     * Permet de crée une doc
     * @IsGranted("ROLE_Lspd_CHIEF")
     * @Route("/newDocs", name="new_Docs")
     */
    public function newDocs(EntityManagerInterface $manager, Request $request)
    {
        $doc = new Docs();
        $user = $this->getUser();
        $userRoles = $user->getRoles();
        $form = $this->createForm(DocsType::class, $doc);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!isset($_POST['tag'])) {
                $this->addFlash(
                    'danger',
                    'Aucune catégorie de sélectionner'
                );
                return $this->redirectToRoute('new_Docs');
            }
            $tag = $_POST['tag'];
            if (!in_array($tag, $user->getRoles(), true)) {
                $this->addFlash(
                    'danger',
                    'Vous ne pouvez pas utiliser ce tag'
                );
                return $this->redirectToRoute('new_News');
            }
            $doc->setTags($tag);
            $manager->persist($doc);
            $manager->flush();
            $this->addFlash(
                'success',
                'Création effectuée avec success'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('common/newDocs.html.twig', [
            'form' => $form->createView(),
            'roles' => $userRoles
        ]);
    }

    /**
     * Permet d'afficher une documentation
     * @Route("/docs/{slug}", name="docs_show")
     */
    public function showDocs(Docs $docs)
    {
        $user = $this->getUser();
        $roleUser = $user->getRoles();
        if ($docs->getPrivat()) {
            $docsTagString = $docs->getTags();
            $docTag = explode('_', $docsTagString);
            $userRole = [];
            foreach ($roleUser as $role) {
                $role = explode('_', $role);
                $userRole[] = $role['1'];
            }
            if (!in_array($docTag['1'], $userRole)) {
                $this->addFlash(
                    'warning',
                    'Vous n\'avez pas acces à cette documentation'
                );
                return $this->redirectToRoute('lspd_docs');
            }
        }
        return $this->render('common/docs.html.twig', [
            'docs' => $docs
        ]);
    }

    /**
     * Remove docs
     * @Route("/docs/remove/{slug}", name="docs_remove")
     * @IsGranted({"ROLE_Lspd_CHIEF", "ROLE_Admin"})
     * @param EntityManagerInterface $manager
     * @param Docs $docs
     * @return RedirectResponse|Response
     */
    public function removeDocs(EntityManagerInterface $manager, Docs $docs)
    {
        $user = $this->getUser();
        $roleUser = $user->getRoles();
        if (!in_array($docs->getTags(), $roleUser, true)) {
            $this->addFlash(
                'warning',
                'Vous n\'avez pas les authorisation néssaisaire'
            );
            return $this->redirectToRoute('homepage');
        }
        $manager->remove($docs);
        $manager->flush();
        $this->addFlash(
            'success',
            'Documentation supprimer avec succes'
        );
        return $this->redirectToRoute('homepage');
    }


    /** PARTIE CONTACT */

    /**
     * Permet d'envoyer une demande de contact
     * @Route("/contact", name="new_contact")
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function newContact(EntityManagerInterface $manager, Request $request)
    {
        $user = $this->getUser();
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!isset($_POST['tag'])) {
                $this->addFlash(
                    'danger',
                    'Aucune catégorie de sélectionner'
                );
                return $this->redirectToRoute('new_Docs');
            }
            $tag = $_POST['tag'];
            $contact->setAuthor($user);
            $contact->setTags($tag);
            $manager->persist($contact);
            $manager->flush();
            $this->addFlash(
                'success',
                'Message bien envoyer'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('common/newContact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
