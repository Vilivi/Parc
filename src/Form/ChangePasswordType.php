<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'disabled' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'disabled' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'disabled' => true
            ])
            ->add('old_password', PasswordType::class, [
                'label' => 'Votre mot de passe actuel', 
                'mapped' => false,
                'required' => true,
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre mot de passe actuel'
                    ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Votre nouveau mot de passe',
                'mapped' => false,
                'required' => true,
                'invalid_message' => 'Le mot de passe de confirmation doit être identique au mot de passe.',
                'first_options' => ['label' => 'Votre nouveau mot de passe',
                'attr'=> ['placeholder' => 'Merci de saisir votre nouveau mot de passe']],
                'second_options' => ['label' => 'Confirmez votre nouveau mot de passe',
                'attr'=> ['placeholder' => 'Merci de saisir votre nouveau mot de passe']]
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
            'data_class' => User::class,
        ]);
    }
}
