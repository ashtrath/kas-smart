<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::all();

        return $this->sendResponse('Products retrieved successfully.', $product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->all());

        return $this->sendResponse('Product created successfully.', $product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse('Product retrieved successfully.', $product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->sendError('Product not found.');
        }

        $product->update($request->all());

        return $this->sendResponse('Product updated successfully.', $product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->sendError('Product not found.');
        }

        $product->delete();

        return $this->sendResponse('Product deleted successfully.', null, 204);
    }
}
