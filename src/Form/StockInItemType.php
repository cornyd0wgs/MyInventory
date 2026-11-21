<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Product;
use App\Entity\StockInItem;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StockInItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => static fn (Product $product): string => $product->getName().($product->getBarcode() ? ' · '.$product->getBarcode() : ''),
                'query_builder' => static fn (ProductRepository $repository) => $repository->createQueryBuilder('p')
                    ->andWhere('p.isArchived = false')
                    ->orderBy('p.name', 'ASC'),
                'placeholder' => 'Choose a product',
                'attr' => ['class' => 'form-select', 'data-product-select' => ''],
            ])
            ->add('quantity', IntegerType::class, [
                'empty_data' => 0,
                'attr' => ['class' => 'form-control', 'min' => 1, 'data-quantity' => ''],
            ])
            ->add('unitCost', TextType::class, [
                'empty_data' => '',
                'attr' => ['class' => 'form-control', 'inputmode' => 'decimal', 'data-unit-cost' => ''],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockInItem::class,
        ]);
    }
}
