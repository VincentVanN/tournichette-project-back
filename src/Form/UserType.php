<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'invalid_message' => 'Cette adresse e-mail n\'est pas valide.',
                'label' => 'E-mail',
                'help' => 'Obligatoire'
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Client' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN',
                ],
                'choice_attr' => [
                    'Client' => ['checked' => 'checked']
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs ne sont pas identiques.',
                'first_options' => ['label' => 'Mot de passe', 'help' => 'obligatoire'],
                'second_options' => ['label' => 'Répétez le mot de passe']
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone'
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse'
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPresetDatas']);
    }

    public function onPresetDatas(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (!is_null($user->getId())) {
            $roleName = $user->getRoleName();

            $form->remove('password');
            $form->remove('roles');
            $form->remove('email');
            $form->add('email', EmailType::class, [
                'invalid_message' => 'Cette adresse e-mail n\'est pas valide.',
                'label' => 'E-mail'
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Client' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN',
                ],
                'choice_attr' => [
                    $roleName => ['checked' => 'checked']
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs ne sont pas identiques.',
                'first_options' => ['label' => 'Mot de passe', 'help' => 'Laissez vide si inchangé'],
                'second_options' => ['label' => 'Répétez le mot de passe'],
                'mapped' => false
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
