<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\StockIn;
use App\Entity\Supplier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StockInType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referenceNumber', TextType::class, [
                'empty_data' => '',
                'attr' => ['class' => 'form-control', 'placeholder' => 'SI-20261121-001'],
            ])
            ->add('supplier', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'None',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => StockInItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label' => false,
                'attr' => ['data-stock-items' => ''],
            ])
            ->add('barcode', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Scan barcode',
                'attr' => [
                    'class' => 'form-control',
                    'autofocus' => 'autofocus',
                    'inputmode' => 'numeric',
                    'placeholder' => 'Scan or type an exact barcode, then press Enter',
                    'data-stock-barcode' => '',
                ],
            ])
            ->add('updateCostPrice', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Update product cost price',
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label'],
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockIn::class,
        ]);
    }
}
