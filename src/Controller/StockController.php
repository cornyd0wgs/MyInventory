<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\BarcodeLookup;
use App\Entity\Product;
use App\Entity\StockIn;
use App\Entity\StockInItem;
use App\Entity\User;
use App\Form\StockInType;
use App\Repository\ProductRepository;
use App\Repository\StockInRepository;
use App\Service\StockService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EMPLOYEE')]
final class StockController extends AbstractController
{
    #[Route('/stock', name: 'app_stock_index', methods: ['GET'])]
    public function index(StockInRepository $stockIns): Response
    {
        $records = $stockIns->findAllWithDetails();
        $ids = array_values(array_filter(array_map(
            static fn (StockIn $stockIn): ?int => $stockIn->getId(),
            $records
        )));

        return $this->render('stock/index.html.twig', [
            'stockIns' => $records,
            'itemCounts' => $stockIns->itemCounts($ids),
        ]);
    }

    #[Route('/stock/in', name: 'app_stock_in_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StockService $stockService): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $stockIn = (new StockIn())->setReceivedBy($user);
        if (!$request->isMethod('POST')) {
            $stockIn->addItem(new StockInItem());
        }

        $form = $this->createForm(StockInType::class, $stockIn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Please correct the highlighted errors.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $stockService->recordStockIn($stockIn, (bool) $form->get('updateCostPrice')->getData());
                $this->addFlash('success', 'Stock-in recorded.');

                return $this->redirectToRoute('app_stock_index');
            } catch (\Throwable) {
                $this->addFlash('danger', 'Stock-in was not saved. No quantities were changed.');
            }
        }

        return $this->render('stock/in.html.twig', [
            'form' => $form,
            'stockIn' => $stockIn,
        ]);
    }

    #[Route('/stock/lookup', name: 'app_stock_lookup', methods: ['GET'])]
    public function lookup(#[MapQueryString] BarcodeLookup $lookup, ProductRepository $products): JsonResponse
    {
        $barcode = trim($lookup->barcode ?? '');
        $product = $products->findOneBy(['barcode' => $barcode]);

        if (!$product instanceof Product) {
            return new JsonResponse(['error' => 'No active product found for that barcode.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'barcode' => $product->getBarcode(),
            'unitCost' => $product->getCostPrice(),
        ]);
    }

    #[Route('/stock/{id}', name: 'app_stock_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, StockInRepository $stockIns): Response
    {
        $stockIn = $stockIns->findOneWithDetails($id);
        if (!$stockIn instanceof StockIn) {
            throw $this->createNotFoundException('Stock-in not found.');
        }

        return $this->render('stock/show.html.twig', [
            'stockIn' => $stockIn,
        ]);
    }
}
