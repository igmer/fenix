<?php

namespace App\Form;

use App\Entity\Cobrador;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;


class CobradorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cedula', TextType::class)
            ->add('nombre', TextType::class)
            ->add('direccion',TextType::class)
            ->add('barrio', TextType::class)
            ->add('fijo', TextType::class)
            ->add('celular', TextType::class)
            ->add('username',TextType::class, array('mapped'=>false))
            ->add('email',TextType::class)
            ->add('guardar', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cobrador::class,
        ]);
    }
}
