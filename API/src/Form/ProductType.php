<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Utils\MySlugger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name')
        ->add('stock')
        ->add('unity')
        ->add('price')
        ->add('slug')
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => true
            //'mapped' => false,
        ]);
         
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

    
}
