<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Utils\MySlugger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', null, [
            'label' => 'nom',
        ])
        ->add('description', TextType::class, [
            'label' => 'Description'
        ])
        ->add('stock', null, [
            'label' => 'Quantité en vente'
        ])
        ->add('unity', ChoiceType::class, [
            'label' => 'Unité',
            'choices' => [
                'Lots' => 'lot(s)',
                'Kg' => 'Kg',
                'Grammes' => 'g',
                'Bouteilles' => 'btlle(s)',
                'Sachets' => 'sachet(s)',
                'Pots' => 'pot(s)',
                'Bottes' => 'botte(s)',
                'Pièce' => 'pièce'
            ]
        ])
        ->add('price', null, [
            'label' => 'Prix (à l\'unité)'
        ])
        ->add('image')
        ->add('category', EntityType::class, [
            'label' => 'Catégorie',
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
