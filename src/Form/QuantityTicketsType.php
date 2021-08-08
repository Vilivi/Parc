<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class QuantityTicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('1', IntegerType::class, [
                'label' => '- de 10ans',
                'data' => 0,
                'required' => false,
                'constraints' => new Length([
                    'min' => 0, 'max' => 20
                    ]), 
                'attr' => [
                    'class' => 'col-4'
                ]
            ])
            ->add('2', IntegerType::class, [
                'label' => '+ de 10ans',
                'data' => 0,
                'required' => false,
                'constraints' => new Length([
                    'min' => 0, 'max' => 20
                    ]), 
                'attr' => [
                    'class' => 'col-4'
                ]
            ])
            ->add('3', IntegerType::class, [
                'label' => 'Adulte',
                'data' => 0,
                'required' => false,
                'constraints' => new Length([
                    'min' => 0, 'max' => 20
                    ]), 
                'attr' => [
                    'class' => 'col-4'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn-success mt-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
