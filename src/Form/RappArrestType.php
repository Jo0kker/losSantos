<?php

namespace App\Form;

use App\Entity\RappArrest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RappArrestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateArrest')
            ->add('userArrest')
            ->add('lieux')
            ->add('otherUsers')
            ->add('createdAt')
            ->add('author')
            ->add('name')
            ->add('prenom')
            ->add('adress')
            ->add('permiNum')
            ->add('birthDay')
            ->add('img')
            ->add('sexe')
            ->add('charge')
            ->add('amande')
            ->add('peine')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RappArrest::class,
        ]);
    }
}
