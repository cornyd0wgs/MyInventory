<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    /** @var array<string, Category> */
    private array $categories = [];

    public function load(ObjectManager $manager): void
    {
        $this->loadCategories($manager);
        $this->loadSuppliers($manager);
        $this->loadProducts($manager);

        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager): void
    {
        $descriptions = [
            'Beverages' => 'Soft drinks, juices, and ready-to-drink tea.',
            'Canned Goods' => 'Tuna, sardines, and canned fruit.',
            'Instant Noodles' => 'Cup and packet noodle products.',
            'Snacks' => 'Chips, crackers, and biscuits.',
            'Toiletries' => 'Personal care and hygiene items.',
        ];

        $repository = $manager->getRepository(Category::class);
        foreach ($descriptions as $name => $description) {
            $category = $repository->findOneBy(['name' => $name]);
            if (!$category instanceof Category) {
                $category = (new Category())->setName($name);
                $manager->persist($category);
            }

            $category->setDescription($description);
            $this->categories[$name] = $category;
        }

        $manager->flush();
    }

    private function loadSuppliers(ObjectManager $manager): void
    {
        $data = [
            'Metro Distributors Inc.' => ['Mia Santos', '02 8123 4567'],
            'Island Traders Supply' => ['Jorge Reyes', '02 8765 4321'],
        ];

        $repository = $manager->getRepository(Supplier::class);
        foreach ($data as $name => [$contactPerson, $contactNumber]) {
            $supplier = $repository->findOneBy(['name' => $name]);
            if (!$supplier instanceof Supplier) {
                $supplier = (new Supplier())->setName($name);
                $manager->persist($supplier);
            }

            $supplier->setContactPerson($contactPerson)->setContactNumber($contactNumber);
        }

        $manager->flush();
    }

    private function loadProducts(ObjectManager $manager): void
    {
        $rows = [
            ['Beverages', '4800012345001', 'Coca-Cola 330ml', '12.00', '18.00', 24, 12, null],
            ['Beverages', '4800012345002', 'Sprite 330ml', '11.50', '17.00', 3, 12, null],
            ['Beverages', '4800012345003', 'Royal Tru-Orange 330ml', '11.50', '17.00', 18, 12, null],
            ['Beverages', '4800012345004', 'Nestea Iced Tea 1L', '28.00', '42.00', 10, 8, 45],
            ['Canned Goods', '4800012345005', 'Century Tuna 155g', '38.00', '55.00', 0, 10, null],
            ['Canned Goods', '4800012345006', '555 Sardines 155g', '24.00', '36.00', 30, 15, null],
            ['Canned Goods', '4800012345007', 'Argentina Sardines 155g', '25.00', '37.00', 8, 15, 120],
            ['Canned Goods', '4800012345008', 'Del Monte Fruit Cocktail 215g', '32.00', '48.00', 14, 10, 90],
            ['Instant Noodles', '4800012345009', 'Indomie Mi Goreng 85g', '8.50', '15.00', 40, 20, null],
            ['Instant Noodles', '4800012345010', 'Lucky Me Pancit Canton 65g', '9.00', '16.00', 6, 20, null],
            ['Instant Noodles', '4800012345011', 'Maggi 2-Minute Tom Yum 70g', '9.50', '17.00', 25, 20, 60],
            ['Instant Noodles', '4800012345012', 'Nissin Cup Noodles 75g', '16.00', '28.00', 4, 12, null],
            ['Snacks', '4800012345013', 'Piattos Cheese 85g', '20.00', '35.00', 22, 10, null],
            ['Snacks', '4800012345014', 'Nova Chicharon 65g', '18.00', '32.00', 5, 10, 20],
            ['Snacks', '4800012345015', 'Rebisco Vanilla 45g', '10.00', '18.00', 36, 15, null],
            ['Snacks', '4800012345016', 'Skyflakes Crackers 40g', '12.00', '22.00', 9, 15, 14],
            ['Toiletries', '4800012345017', 'Safeguard Soap 125g', '32.00', '49.00', 18, 8, null],
            ['Toiletries', '4800012345018', 'Dove Shampoo 180ml', '88.00', '135.00', 2, 6, 300],
            ['Toiletries', '4800012345019', 'Colgate Toothpaste 150ml', '65.00', '99.00', 16, 8, null],
            ['Toiletries', '4800012345020', 'Pantene Conditioner 170ml', '92.00', '145.00', 7, 6, null],
        ];

        $repository = $manager->getRepository(Product::class);
        foreach ($rows as [$categoryName, $barcode, $name, $cost, $selling, $quantity, $reorderLevel, $expiresInDays]) {
            $product = $repository->findOneBy(['barcode' => $barcode]);
            if (!$product instanceof Product) {
                $product = (new Product())->setBarcode($barcode);
                $manager->persist($product);
            }

            $expirationDate = null === $expiresInDays
                ? null
                : new \DateTimeImmutable(sprintf('+%d days', $expiresInDays));

            $product
                ->setCategory($this->categories[$categoryName])
                ->setName($name)
                ->setCostPrice($cost)
                ->setSellingPrice($selling)
                ->setQuantity($quantity)
                ->setReorderLevel($reorderLevel)
                ->setExpirationDate($expirationDate)
                ->setIsArchived(false);
        }
    }
}
