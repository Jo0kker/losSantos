<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LspdController extends AbstractController
{
    /**
     * Permet d'afficher les news de la police
     * @Route("/lspd", name="lspd_news")
     */
    public function news(NewsRepository $repo)
    {
        $news = $repo->findBy([
            'tags' => 'ROLE_Lspd'
        ], [
            'id' => 'DESC'
        ]);
        return $this->render('lspd/news.html.twig', [
            'news' => $news,
        ]);
    }
}
