<?php

namespace App\Controller;

use App\Entity\Roles;
use App\Entity\Users;
use App\Form\ProfilType;
use App\Form\RegisterType;
use App\Service\Test;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Filesystem\Filesystem;


class AccountController extends AbstractController
{

    /**
     * Connexion
     * @Route("/login", name="login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError' => $error !== null,
            'username' => $username
        ]);
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
            $user->setRoles(array('ROLE_Civil'));
            $user->setAvatar('assets/img/avatars/avatar5.jpeg');
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('account');
        }
        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Page de gestion du profil
     * @Route("/account", name="account")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function profil(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $previousAvatar = $user->getAvatar();
        $userAvatar = $user->getAvatar();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form['avatar']->getData();
            if ($avatar) {
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$avatar->guessExtension();
                try {
                    if ($previousAvatar !== 'assets/img/avatars/avatar5.jpeg') {
                        $filesystem = new Filesystem();
                        $filesystem->remove($userAvatar);
                    }
                    $avatar->move($this->getParameter('avatar_directory'), $newFilename);
                } catch (FileException $e) {

                }
                $chemin = 'uploads/avatar/' . $newFilename;
                $user->setAvatar($chemin);
            }else {
                $user->setAvatar($userAvatar);
            }
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                "success",
                'Modification effectuée'
            );
            return $this->redirectToRoute('account');
        }
        return $this->render('account/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
