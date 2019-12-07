<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{

    /**
     * Connexion
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('account/login.html.twig');
    }

    /**
     * Pour la déco
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {

    }

    /**
     * Permet de crer un compte
     * @Route("/register", name="register")
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function register(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, Request $request)
    {
        $user = new Users();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pwd = $encoder->encodePassword($user, $user->getPwd());
            $user->setPwd($pwd);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('/account');
        }
        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Page de gestion du profil
     * @Route("/account", name="account")
     */
    public function index()
    {
        return $this->render('account/profil.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
}
