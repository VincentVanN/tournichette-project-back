<?php

namespace App\Form;

use App\Entity\Product;
use App\Utils\MySlugger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name',TextType::class,['label' => 'name '])
        ->add('slug', TextType::class, ['label' => 'slug : '])
            ->add('stock', TextType::class, ['label' => 'slug : '])
            ->add('unity', TextType::class, ['label' => 'unity : '])
            ->add('price',TextType::class, ['label' => 'price : '])
            ->add('category', TextType::class, ['label' => 'category : ']);
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

    ->add('name',TextType::class,['label' => 'name '])
            ->add('pa',TextType::class, ['label' => 'Prix Achat : '])
            ->add('pv',TextType::class, ['label' => 'Prix Vente : '])
            ->add('tva',TextType::class,['label' => 'Tva : '])
            ->add('stock',TextType::class,['label' => 'Stock : '])
            ->add('image' , FileType::class, ['label' => 'Image : '] );
}
