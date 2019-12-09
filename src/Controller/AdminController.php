<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user")
     * @param UsersRepository $repo
     * @return Response
     */
    public function show_membre(UsersRepository $repo): Response
    {
        $users = $repo->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Permet d'ajouter un role
     * @Route("/admin/addRole", name="admin_addRole")
     * @param $id
     * @param $role
     * @param EntityManagerInterface $manager
     * @param UsersRepository $repo
     * @return RedirectResponse
     */
    public function addRole(EntityManagerInterface $manager, UsersRepository $repo)
    {
        $id = $_POST['id'];
        $role = $_POST['role'];
        $user = $repo->findOneBy(['id' => $id]);
        if (in_array($role, $user->getRoles(), true)) {
            $this->addFlash(
                'warning',
                'La personne a déjà ce role'
            );
            return $this->redirectToRoute('admin_user');
        }else{
            $newRole = $user->getRoles();
            array_push($newRole, $role);
            $user->setRoles($newRole);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Role ajouter avec success'
            );
            return $this->redirectToRoute('admin_user');
        }
    }

    /**
     * For remove Role
     * @Route("/admin/removeRole/{id}/{roles}", name="remove_role")
     * @param $id
     * @param $roles
     * @param EntityManagerInterface $manager
     * @param UsersRepository $repository
     * @return RedirectResponse
     */
    public function removeRole($id, $roles, EntityManagerInterface $manager, UsersRepository $repository): RedirectResponse
    {
        $user = $repository->findOneBy(['id' => $id]);
        if (in_array($roles, $user->getRoles(), true)) {
            $roleMod = $user->getRoles();
            unset($roleMod[array_search($roles, $roleMod, true)]);
            $user->setRoles($roleMod);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('admin_user');
        }else{
            $this->addFlash(
                'danger',
                'Erreur dans la selection du role a supprimer'
            );
            return $this->redirectToRoute('admin_user');
        }
    }

    /**
     * Permet de passer le compte admin
     * @Route("/G6t9jW3h2BmcmkUZizvg", name="G6t9jW3h2BmcmkUZizvg")
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function setAdmin(EntityManagerInterface $manager): RedirectResponse
    {
        $user = $this->getUser();
        $rolesActuel = $user->getRoles();
        if (in_array('ROLE_Admin', $rolesActuel, true)) {
            $this->addFlash(
                'warning',
                'Vous avez déjà le role Admin'
            );
            return $this->redirectToRoute('homepage');
        }
        $rolesActuel[] = 'ROLE_Admin';
        $user->setRoles($rolesActuel);
        $manager->persist($user);
        $manager->flush();
        $this->addFlash(
            'success',
            'Passage en role admin effectuée.'
        );
        return $this->redirectToRoute('homepage');
    }
}
