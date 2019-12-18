<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Entity\MailSend;
use App\Form\MailType;
use App\Repository\MailRepository;
use App\Repository\MailSendRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use function MongoDB\BSON\toJSON;

class MailController extends AbstractController
{
    /**
     * Permet de lister les mails
     * @Route("/mail", name="mail")
     */
    public function index(MailRepository $repoMail, MailSendRepository $repoSend)
    {
        $user = $this->getUser();
        if ($_GET) {
            if ($_GET['doc'] === 'send') {
                $mail = $repoSend->findBy([
                    'author' => $user,
                    'dossier' => 'send'
                ], [
                    'createdAt' => 'DESC'
                ]);
                return $this->render('mail/index.html.twig', [
                    'mails' => $mail,
                    'state' => 'send'
                ]);
            }
            if ($_GET['doc'] === 'del') {
                $mailDel = [];
                $allMailDel = $repoMail->findBy(['dossier' => 'del'], ['createdAt'=>'DESC']);
                foreach ($allMailDel as $mails) {
                    $value = $mails->getDest()->getValues();
                    foreach ($value as $test) {
                        if ($test->getId() == $user->getId()) {
                            $mailDel[] = $mails;
                        }
                    }
                }
                return $this->render('mail/index.html.twig', [
                    'mails' => $mailDel,
                    'state' => 'del'
                ]);
            }
        }
        $mail = [];
        $allMail = $repoMail->findBy(['dossier' => 'recep'], ['createdAt'=>'DESC']);
        foreach ($allMail as $mails) {
            $value = $mails->getDest()->getValues();
            foreach ($value as $test) {
                if ($test->getId() == $user->getId()) {
                    $mail[] = $mails;
                }
            }
        }
        return $this->render('mail/index.html.twig', [
            'mails' => $mail,
            'state' => 'recep'
        ]);
    }

    /**
     * Permet d'envoyer un mail
     * @Route("mail/new", name="new_Mail")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function mailSend(EntityManagerInterface $manager, Request $request, UsersRepository $repo): Response
    {
        $user = $this->getUser();
        $listUsers = [];
        $listUsers1 = $repo->findAll();
        if ($_GET and is_numeric($_GET['i'])){
            $i = (int)$_GET['i'];
            $listUsers = $repo->findBy([
                'id' => $i
            ]);
        }else{
            $listUsers = $listUsers1;
        }
        $mail = new Mail();
        $form = $this->createForm(MailType::class, $mail);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dest = $_POST['dest'];

            if (!$dest) {
                $this->addFlash(
                    'danger',
                    'Erreur dans le choix du destinataire'
                );
                return $this->redirectToRoute('new_Mail');
            }
            $mailDest = $repo->findOneBy([
                'id' => $dest
            ],[]);
            $token = uniqid();
            $mail->setToken($token);
            $mail->addDest($mailDest);
            $mail->setLu(false);
            $mail->setAuthor($user);
            $mail->setDossier('recep');
            $mailSend = new MailSend();
            $mailSend
                ->setAuthor($user)
                ->addDest($mailDest)
                ->setObjet($mail->getObjet())
                ->setMsg($mail->getMsg())
                ->setLu(true)
                ->setDossier('send')
                ->setToken($token)
             ;
            $manager->persist($mail);
            $manager->persist($mailSend);
            $manager->flush();
            $this->addFlash(
                'success',
                'Message envoyer'
            );
            return $this->redirectToRoute('mail');
        }
        return $this->render('mail/newMail.html.twig', [
            'form' => $form->createView(),
            'listUsers' => $listUsers
        ]);
    }

    /**
     * Permet de récuperer le contenu du message
     * @Route("/mail/mgsContent/{token}", name="mail_content")
     * @param Mail $mail
     * @return Response
     */
        public function takeMsgContent(Mail $mail, EntityManagerInterface $manager)
    {
        $IdDest = [];
        foreach ($mail->getDest() as $test){
            $IdDest[] = $test->getId();
        }
        if (in_array($this->getUser()->getId(), $IdDest, true) xor $this->getUser()->getId() === $mail->getAuthor()->getId()){
            if (isset($_GET['s']) and ($_GET['s'] === 'recep')){
                if (!$mail->getLu()) {
                    $mail->setLu(true);
                    $manager->persist($mail);
                    $manager->flush();
                }
            }
            return $this->render('mail/show.html.twig', [
                'mail' => $mail
            ]);
        }
        $this->addFlash(
            'danger',
            'Il est interdit de jouer avec les mails des autres'
        );
        return $this->redirectToRoute('mail');

    }

    /**
     * for remove mail
     * @Route("/mail/remove/{token}", name="mail_remove")
     */
    public function removeMail($token, MailRepository $repoMail, MailSendRepository $repoMailSend, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        if ($_GET){
            if ($_GET['s'] === 'recep' or $_GET['s'] === 'del'){
                $mail = $repoMail->findOneBy(['token'=> $token],[]);
                foreach ($mail->getDest() as $mailDest){
                    if ($user->getId() === $mailDest->getId()) {
                        if ($_GET['s'] === 'del'){
                            $manager->remove($mail);
                            $manager->flush();
                            $this->addFlash(
                                'success',
                                'Message supprimer'
                            );
                        return $this->redirectToRoute('mail');
                        }else{
                            $mail->setDossier('del');
                            $manager->persist($mail);
                            $manager->flush();
                            $this->addFlash(
                                'success',
                                'Message supprimer'
                            );
                            return $this->redirectToRoute('mail');
                        }
                    }
                }
            }elseif ($_GET['s'] === 'send'){
                $mail = $repoMailSend->findOneBy(['token'=>$token],[]);
                if ($mail->getAuthor()->getId() !== $user->getId()) {
                    $this->addFlash(
                        'danger',
                        'Merci de ne pas jouer avec les mails'
                    );
                    return $this->redirectToRoute('mail');
                }
                $manager->remove($mail);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Message supprimer'
                );
                return $this->redirectToRoute('mail');
            }
        }
        return $this->redirectToRoute('mail');
    }
}
