<?php

namespace App\Form;

use App\Entity\SalesStatus;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startMail', CKEditorType::class, [
                'label' => 'Corps du message',
                'attr' => ['rows' => '10']
            ])
            ->add('endMail', CKEditorType::class, [
                'label' => 'Corps du message',
                'attr' => ['rows' => '10']
            ])
            ->add('startMailSubject', TextType::class, [
                'label' => 'Sujet'
            ])
            ->add('endMailSubject', TextType::class, [
                'label' => 'Sujet'
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
