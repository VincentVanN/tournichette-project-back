<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Administrateur' => 'ROLE_SUPER_ADMIN',
                    'Manager' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                "expanded" => true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs ne sont pas identiques',
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Resaisissez votre mot de passe'],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetDataPassword'])
        ;
    }

    public function onPreSetDataPassword (FormEvent $event) {
        $user = $event->getData();
        $form = $event->getForm();

        // dd($user, $form);
        if (! is_null($user->getId()))
        {
            $form->remove('password');
            $form->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs ne sont pas identiques',
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => 'laisser vide si inchangé'],
                ],
                'second_options' => ['label' => 'Resaisissez votre mot de passe'],
                'mapped' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
