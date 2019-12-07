<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'attr'=> [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Votre nom'
                ],
            ])
            ->add('prenom', TextType::class, [
                'attr'=> [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Votre Prenom'
                ]
            ])
            ->add('discord', TextType::class, [
                'attr'=> [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Votre mail discord'
                ]
            ])
            ->add('pwd', PasswordType::class, [
                'attr'=> [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Mot de passe'
                ]
            ])
            ->add('pwdConf', PasswordType::class, [
                'attr'=> [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Confirmation'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
