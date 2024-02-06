<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index() : JsonResponse
    {
        return $this->productService->getProducts(10);
    }

    public function store(ProductRequest $request) : JsonResponse
    {
        return $this->productService->insertProduct($request->all());
    }


    public function show(int $id) : JsonResponse
    {
        return $this->productService->getProductById($id);
    }

    public function update(int $id,ProductRequest $request) : JsonResponse
    {
        return $this->productService->updateProduct($id,$request->all());
    }

    public function destroy(int $id) : JsonResponse
    {
        return $this->productService->deleteProduct($id);
    }
}
