<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, 
                ['label' => 'Votre prénom', 
                'constraints' => new Length([
                    'min' => 2, 'max' => 30
                    ]),
                'attr' => ['placeholder' => 'Merci de saisir votre nom']    
            ])
            ->add('lastname', Texttype::class, 
                ['label' => 'Votre nom', 
                'constraints' => new Length([
                    'min' => 2, 'max' => 30
                    ]),
                'attr' => ['placeholder' => 'Merci de saisir votre nom']    
            ])
            ->add('email', EmailType::class, 
                ['label' => 'Votre email', 
                'constraints' => new Length([
                    'min' => 2, 'max' => 55
                    ]),
                'attr' => ['placeholder' => 'Merci de saisir votre email']    
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Votre age',
                'constraints' => new Length([
                    'min' => 1, 'max' => 3
                ]),
                'attr' => [
                    'placeholder' => 'Merci de saisir votre âge'
                ]
            ])
            ->add('pseudo', TextType::class, 
                ['label' => 'Choisissez un pseudo', 
                'constraints' => new Length([
                    'min' => 2, 'max' => 30
                    ]),
                'attr' => ['placeholder' => 'Merci de saisir un pseudo']    
            ])
            ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'label' => 'Votre mot de passe',
            'required' => true,
            'invalid_message' => 'Le mot de passe de confirmation doit être identique au mot de passe.',
            'first_options' => ['label' => 'Votre mot de passe',
            'attr'=> ['placeholder' => 'Merci de saisir votre mot de passe']],
            'second_options' => ['label' => 'Confirmez votre mot de passe',
            'attr'=> ['placeholder' => 'Merci de saisir votre mot de passe']]
            ])
            ->add('submit', SubmitType::class,
            ['label' => 'S\'inscrire',
            'attr' => [
                'class' => 'btn btn-success'
            ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
