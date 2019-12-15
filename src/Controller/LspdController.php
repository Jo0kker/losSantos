<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Docs;
use App\Entity\RappArrest;
use App\Form\RappArrestType;
use App\Repository\ContactRepository;
use App\Repository\DocsRepository;
use App\Repository\NewsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function docs(DocsRepository $repo): Response
    {
        $user = $this->getUser();
        $userRoles = $user->getRoles();
        $chief = false;
        if (in_array('ROLE_Lspd', $userRoles) or in_array('ROLE_Lspd_CHIEF', $userRoles)) {
            $docs = $repo->findBy([
                'tags' => 'ROLE_lspd_CHIEF'
            ], [
                'createdAt' => 'DESC'
            ]);
            if (in_array('ROLE_Lspd_CHIEF', $userRoles)){
                $chief = true;
            }
        }else{
            $docs = $repo->findBy([
                'tags' => 'ROLE_Lspd_CHIEF',
                'privat' => false,
            ],[
                'createdAt' => 'DESC'
            ]);
        }

        return $this->render('lspd/docs.html.twig', [
            'docs' => $docs,
            'nameTag' => 'LSPD',
            'chief' => $chief
        ]);
    }

    /**
     * Permet d'afficher les contact de la lspd
     * @Route("/lspd/contact", name="lspd_contact")
     * @param ContactRepository $repo
     * @return RedirectResponse|Response
     */
    public function showContact(ContactRepository $repo)
    {
        $contact = $repo->findBy([
            'tags' => 'ROLE_Lspd'
        ], [
            'createdAt' => 'ASC'
        ]);
        if (isset($_GET['id']) and is_numeric($_GET['id'])) {
            $content = $repo->findOneBy([
                'id' => $_GET['id']
            ],[]);
            if ($content == null) {
                $this->addFlash(
                    'danger',
                    'Demande de contact non trouvée'
                );
                return $this->redirectToRoute('lspd_contact');
            }
            if ($content->getTags() != 'ROLE_Lspd') {
                $this->addFlash(
                    'warning',
                    'Cette demande ne conserne pas votre service'
                );
                return $this->redirectToRoute('lspd_contact');
            }
        }else{
            $content['objet'] = 'Pas de message sélectionné';
        }
        return $this->render('lspd/contact.html.twig', [
            'contacts' => $contact,
            'content' => $content
        ]);
    }

    /**
     * Delete contact entity
     * @Route("/lspd/contact/delete", name="lspd_contact_delete")
     * @param EntityManagerInterface $manager
     * @param ContactRepository $repo
     * @return RedirectResponse
     */
    public function delContact(EntityManagerInterface $manager, ContactRepository $repo): RedirectResponse
    {
        $contactid = htmlspecialchars(trim($_GET['id']));
        $contact = $repo->findOneBy([
            'id' => $contactid
        ],[]);
        if (($contact == null) or ($contact->getTags() !== 'ROLE_Lspd')) {
            $this->addFlash(
                'danger',
                'Action non authoriser'
            );
            return $this->redirectToRoute('lspd_contact');
        }
        $manager->remove($contact);
        $manager->flush();
        $this->addFlash(
            'success',
            'Contact supprimer avec succces'
        );
        return $this->redirectToRoute('lspd_contact');
    }

    /**
     * Permet d'administrer les membres de la lspd
     * @Route("/lspd/membres", name="lspd_membres")
     * @param EntityManagerInterface $manager
     * @param UsersRepository $repo
     * @IsGranted("ROLE_Lspd_CHIEF")
     * @return Response
     */
    public function modUsers(EntityManagerInterface $manager, UsersRepository $repo): Response
    {
        $result = [];
        $allUser = $repo->findAll();
        foreach ($allUser as $user) {
            if (in_array('ROLE_Lspd', $user->getRoles())){
                $result[] = $user;
            }
        }
        return $this->render('lspd/membres.html.twig', [
            'users' => $result
        ]);
    }

    /**
     * Création d'un nouveau rapport d'arrestation
     * @Route("/lspd/newRapportArrest", name="lspd_newRapportArrest")
     * @param EntityManagerInterface $manager
     */
    public function newRapportArrest(EntityManagerInterface $manager)
    {
        $rapportArrest = new RappArrest();
        $form = $this->createForm(RappArrestType::class, $rapportArrest);

        return $this->render('/lspd/newArrest.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
