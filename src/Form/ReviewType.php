<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Votre pseudo',
                'constraints' => new Length([
                    'min' => 2, 'max' => 30
                    ]),
                'attr' => ['placeholder' => 'Merci de saisir un pseudo'] 
            ])
            ->add('title', TextType::class, [
                'label' => 'Sujet de votre commentaire',
                'required' => true,
                'constraints' => new Length([
                    'min' => 2, 'max' => 30
                    ]),
            ])
            ->add('comment', TextType::class, [
                'label' => 'Votre commentaire',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ecrivez votre commentaire'
                ]
            ] )
            ->add('notation', IntegerType::class, [
                'label' => 'Choisissez une note entre 0 et 5',
                'constraints' => new Length([
                    'min' => 0, 'max' => 5
                ]),
                'attr' => [
                    'placeholder' => 'Merci de choisir une note'
                ]
            ])
            ->add('submit', SubmitType::class,  [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-success mt-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
