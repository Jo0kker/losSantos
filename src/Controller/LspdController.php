<?php

namespace App\Controller;

use App\Repository\DocsRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LspdController extends AbstractController
{
    /**
     * Permet d'afficher les documents
     * @Route("/lspd/docs", name="lspd_docs")
     * @param DocsRepository $repo
     * @return Response
     */
    public function docs(DocsRepository $repo)
    {
        $docs = $repo->findBy([
            'tags' => 'ROLE_lspd_CHIEF'
        ], [
            'createdAt' => 'DESC'
        ]);
        return $this->render('lspd/docs.html.twig', [
            'docs' => $docs,
            'nameTag' => 'LSPD'
        ]);
    }
}
