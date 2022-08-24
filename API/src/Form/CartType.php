<?php

namespace App\Form;

use App\Entity\Cart;
use App\Utils\MySlugger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('dateOrder')
            ->add('typeCart')
            ->add('price');
            //->add('paidAt');
            //->add('paymentStatus')
           // ->add('address')
            //->add('user')
            //->add('deliverSatus')  ;     
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}