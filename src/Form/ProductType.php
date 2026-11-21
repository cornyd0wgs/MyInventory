<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a category',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('barcode', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'inputmode' => 'numeric'],
            ])
            ->add('costPrice', TextType::class, [
                'attr' => ['class' => 'form-control', 'inputmode' => 'decimal'],
            ])
            ->add('sellingPrice', TextType::class, [
                'attr' => ['class' => 'form-control', 'inputmode' => 'decimal'],
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('reorderLevel', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('expirationDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);

        $builder->get('barcode')->addModelTransformer(new CallbackTransformer(
            static fn (?string $barcode): ?string => $barcode,
            static fn (?string $barcode): ?string => (null === $barcode || '' === trim($barcode)) ? null : trim($barcode),
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
