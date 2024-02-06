<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use App\Api\ApiMessages;


class ProductService
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProducts(int $qtd): JsonResponse
    {
        return response()->json($this->productRepository->findAll($qtd), 200);
    }

    public function getProductById(int $id): JsonResponse
    {
        try {
            return response()->json(['data' => $this->productRepository->findById($id)], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function insertProduct(array $product): JsonResponse
    {
        try {
            $product = $this->productRepository->insert($product);
            return response()->json([
                'message' => 'product added successfully',
                'data' => $product
                ,
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function updateProduct($id, array $product): JsonResponse
    {
        try {
            $productUpdated = $this->productRepository->update($id, $product);
            return response()->json([
                'message' => 'product updated successfully',
                'data' => $productUpdated
                ,
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function deleteProduct(int $id): JsonResponse
    {
        try {
            $this->productRepository->delete($id);
            return response()->json([
                'message' =>  'product deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
