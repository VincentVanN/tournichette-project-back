<?php

namespace App\Form;

use App\Entity\SalesStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startMail', TextareaType::class, [
                'label' => 'Mail d\'ouverture des ventes',
                'attr' => ['rows' => '10']
            ])
            ->add('endMail', TextareaType::class, [
                'label' => 'Mail de fermeture des ventes',
                'attr' => ['rows' => '10']
            ])
            ->add('sendMail', CheckboxType::class, [
                'label' => 'Activer l\'envoi des mails'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalesStatus::class,
        ]);
    }
}
