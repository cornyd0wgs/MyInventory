<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ProductFilter;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product_index', methods: ['GET'])]
    public function index(
        #[MapQueryString] ProductFilter $filter,
        ProductRepository $products,
        CategoryRepository $categories,
    ): Response {
        $search = null !== $filter->q ? trim($filter->q) : null;
        if ('' === $search) {
            $search = null;
        }

        return $this->render('product/index.html.twig', [
            'products' => $products->search($search, $filter->category),
            'categories' => $categories->findAll(),
            'filter' => $filter,
            'lowStockCount' => $products->countLowStock(),
        ]);
    }

    #[Route('/products/low-stock', name: 'app_product_low_stock', methods: ['GET'])]
    public function lowStock(ProductRepository $products): Response
    {
        return $this->render('product/low_stock.html.twig', [
            'products' => $products->lowStock(),
            'lowStockCount' => $products->countLowStock(),
        ]);
    }

    #[Route('/products/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_OWNER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Product added.');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/form.html.twig', [
            'product' => $product,
            'form' => $form,
            'mode' => 'new',
        ]);
    }

    #[Route('/products/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_OWNER')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Product updated.');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/form.html.twig', [
            'product' => $product,
            'form' => $form,
            'mode' => 'edit',
        ]);
    }

    #[Route('/products/{id}/archive', name: 'app_product_archive', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_OWNER')]
    public function archive(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('archive-product'.$product->getId(), $token)) {
            $this->addFlash('danger', 'Invalid archive token.');

            return $this->redirectToRoute('app_product_index');
        }

        $product->setIsArchived(true);
        $entityManager->flush();
        $this->addFlash('success', 'Product archived.');

        return $this->redirectToRoute('app_product_index');
    }
}
