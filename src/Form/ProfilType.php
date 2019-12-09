<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('discord', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('avatar', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'placeholder' => 'Nouvelle image'
                ],
                'label' => 'avatar',
                'data_class' => null,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Format incorrect',
                    ])
                ]
            ])
            ->add('tel', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('job', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
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
