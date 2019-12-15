<?php

namespace App\Form;

use App\Entity\RappArrest;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TextTypeAlias;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RappArrestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateArrest', DateTimeType::class)
            ->add('userArrest', TextTypeAlias::class)
            ->add('lieux', TextTypeAlias::class)
            ->add('otherUsers', TextTypeAlias::class)
            ->add('name', TextTypeAlias::class)
            ->add('prenom', TextTypeAlias::class)
            ->add('adress', TextTypeAlias::class)
            ->add('birthDay', TextTypeAlias::class)
            ->add('img', FileType::class, [
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
            ->add('sexe', TextTypeAlias::class)
            ->add('charge', TextareaType::class, [
                'attr' => [
                    'class' => 'ckeditor'
                ]
            ])
            ->add('amande', TextTypeAlias::class)
            ->add('peine', TextTypeAlias::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RappArrest::class,
        ]);
    }
}
