<?php

namespace App\Form;

use App\Entity\Order;
use App\Utils\MySlugger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateOrder')
            ->add('paidAt')
            ->add('paymentStatus')
            //->add('depot')
            //->add('user')
            ->add('deliverStatus') ;   
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}