<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Utils\MySlugger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', null, [
            'label' => 'Nom',
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description'
        ])
        // Désactivé pour le moment
        // ->add('stock', null, [
        //     'label' => 'Quantité en vente'
        // ]) 
        ->add('unity', ChoiceType::class, [
            'label' => 'Unité',
            'choices' => [
                'Lots' => 'lot(s)',
                'Kg' => 'Kg',
                'Grammes' => 'g',
                'Bouteilles' => 'btlle',
                'Sachets' => 'sachet',
                'Pots' => 'pot',
                'Bottes' => 'botte',
                'Pièce' => 'pièce',
                'Barquettes' => 'barquette'
            ]
        ])
        ->add('price', null, [
            'label' => 'Prix (à l\'unité)'
        ])
        ->add('category', EntityType::class, [
            'label' => 'Catégorie',
            'class' => Category::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => true
            //'mapped' => false,
        ])
        ->add('description');
         
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

    
}
