<?php

namespace App\Controller\Api;

use App\Service\ProductService;
use App\Hydrator\ProductHydrator;
use App\Context\UserContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductController
{
    private ProductService $productService;
    private ProductHydrator $productHydrator;

    public function __construct(ProductService $productService, ProductHydrator $productHydrator)
    {
        $this->productService = $productService;
        $this->productHydrator = $productHydrator;
    }

    /**
     * GET /api/products/{id} - Get a product
     */
    #[Route('/{id}', name: 'api_product_get', methods: ['GET'])]
    public function getProduct(int $id, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $product = $this->productService->getProduct($id, $userContext);

        if ($product === null) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        $data = $this->productHydrator->extract($product);

        return new JsonResponse($data);
    }

    /**
     * GET /api/products/eligible/{customerId} - Get eligible products for a customer
     */
    #[Route('/eligible/{customerId}', name: 'api_product_eligible', methods: ['GET'])]
    public function getEligibleProducts(int $customerId, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $products = $this->productService->getEligibleProducts($customerId, $userContext);

        $results = [];
        foreach ($products as $product) {
            $results[] = $this->productHydrator->extract($product);
        }

        return new JsonResponse([
            'customerId' => $customerId,
            'eligibleProducts' => $results,
            'total' => count($results),
        ]);
    }

    /**
     * GET /api/products/{id}/pricing - Get pricing from multiple sources
     */
    #[Route('/{id}/pricing', name: 'api_product_pricing', methods: ['GET'])]
    public function getPricing(int $id, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $strategy = $request->query->get('strategy', 'average');

        $pricing = $this->productService->getPricing($id, $userContext, $strategy);

        if ($pricing === null) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        return new JsonResponse([
            'productId' => $id,
            'pricing' => $pricing,
            'strategy' => $strategy,
        ]);
    }

    /**
     * POST /api/products/compare - Compare multiple products
     */
    #[Route('/compare', name: 'api_product_compare', methods: ['POST'])]
    public function compareProducts(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $productIds = $data['productIds'] ?? [1, 2, 3];
        
        $userContext = $this->createUserContext($request);
        $comparison = $this->productService->compareProducts($productIds, $userContext);

        return new JsonResponse([
            'comparison' => $comparison,
            'productCount' => count($comparison),
        ]);
    }

    private function createUserContext(Request $request): UserContext
    {
        $role = $request->query->get('role', 'user');
        $userId = (int) $request->query->get('userId', 1);

        $permissions = match($role) {
            'admin' => ['view_all', 'view_pricing'],
            'manager' => ['view_all'],
            default => ['view_basic'],
        };

        return new UserContext($userId, $role, $permissions);
    }
}
