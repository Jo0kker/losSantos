<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Docs;
use App\Entity\RappArrest;
use App\Form\RappArrestType;
use App\Repository\RappArrestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ContactRepository;
use App\Repository\DocsRepository;
use App\Repository\NewsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
     * @IsGranted("ROLE_Lspd")
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
     * @IsGranted("ROLE_Lspd")
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
     * @IsGranted("ROLE_Lspd")
     * @Route("/lspd/membres", name="lspd_membres")
     * @param EntityManagerInterface $manager
     * @param UsersRepository $repo
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
     * Permet d'ajouter un role sur le panneau de la lspd
     * @Route("/lspd/addRoleLspd", name="lspd_addRole")
     * @IsGranted("ROLE_Lspd_CHIEF")
     * @param UsersRepository $repo
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function addRoleLspd(UsersRepository $repo, EntityManagerInterface $manager): ?RedirectResponse
    {
        $id = $_POST['id'];
        $role = htmlspecialchars(trim($_POST['role']));
        $user = $repo->findOneBy(['id' => $id]);
        if (in_array($role, $user->getRoles(), true)) {
            $this->addFlash(
                'warning',
                'La personne a déjà ce role'
            );
            return $this->redirectToRoute('lspd_membres');
        }else{

            $newRole = $user->getRoles();
            $newRole[] = $role;
            $user->setRoles($newRole);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Role ajouter avec success'
            );
            return $this->redirectToRoute('lspd_membres');
        }
    }

    /**
     * Permet de supprimer un role de la lspd
     * @Route("/lspd/removeRole/{id}/{roles}", name="lspd_removeRole")
     * @param $id
     * @param $roles
     * @param UsersRepository $repository
     * @return RedirectResponse
     */
    public function removeRoleLspd($id, $roles, UsersRepository $repository, EntityManagerInterface $manager)
    {
        $user = $repository->findOneBy(['id' => $id]);
        if (in_array($roles, $user->getRoles(), true)) {

            if ($roles === 'ROLE_Lspd' && in_array('ROLE_Lspd_CHIEF', $user->getRoles(), true)) {
                $this->addFlash(
                    'warning',
                    'La personne ne peu etre rester chef sans etre membres'
                );
                return $this->redirectToRoute('lspd_membres');
            }
            $roleMod = $user->getRoles();
            unset($roleMod[array_search($roles, $roleMod, true)]);
            $user->setRoles($roleMod);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Role supprimer avec succes'
            );
            return $this->redirectToRoute('lspd_membres');
        } else {
            $this->addFlash(
                'danger',
                'Erreur dans la selection du role a supprimer'
            );
            return $this->redirectToRoute('lspd_membres');
        }
    }

    /**
     * Création d'un nouveau rapport d'arrestation
     * @Route("/lspd/newRapportArrest", name="lspd_newRapportArrest")
     * @IsGranted("ROLE_Lspd")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function newRapportArrest(EntityManagerInterface $manager, Request $request): Response
    {
        $rapportArrest = new RappArrest();
        $form = $this->createForm(RappArrestType::class, $rapportArrest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $author = $user->getNom() . " " . $user->getPrenom();
            $img = $form['img']->getData();
            if ($img) {
                $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$img->guessExtension();
                try {
                    $img->move($this->getParameter('lspd_arrestation'), $newFilename);
                } catch (FileException $e) {

                }
                $chemin = 'uploads/lspd/rapportArrestation/' . $newFilename;
                $rapportArrest->setImg($chemin);
                $rapportArrest->setAuthor($author);
                $manager->persist($rapportArrest);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Rapport enregistrer'
                );
                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render('/lspd/newArrest.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Voir les rapport d'arrestation
     * @Route("/lspd/rapportArrest", name="lspd_showRapportArrest")
     * @IsGranted("ROLE_Lspd")
     * @param RappArrestRepository $repo
     * @return Response
     */
    public function showRapportArrest(RappArrestRepository $repo): Response
    {
        $rapport = $repo->findAll();
        return $this->render("/lspd/Arrest.html.twig", [
            'rapport' => $rapport
        ]);
    }

    /**
     * Permet de voir un rapport d'arrestation
     * @IsGranted("ROLE_Lspd")
     * @Route("/lspd/rapportArrest/{id}", name="lspd_showDetailRapportArrest")
     * @param RappArrest $arrest
     * @return Response
     */
    public function showRapportArrestDetail(RappArrest $arrest): Response
    {
        return $this->render('lspd/detailArrest.html.twig', [
            'arrest' => $arrest
        ]);
    }

    /**
     * Permet de voir les rapport d'interv
     * @Route("/lspd/rapportInter", name="lspd_showRapportInter")
     */
    public function showRapportInter()
    {

    }
}
