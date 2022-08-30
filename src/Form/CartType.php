<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom'
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix'
            ])
            // ->add('product', EntityType::class, [
            //     'class' => Product::class,
            //     'choice_label' => 'name',
            //     'group_by' => ChoiceList::groupBy($this, 'category'),
            //     'multiple' => false,
            //     'expanded' => false,
            //     'mapped' => false
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}
