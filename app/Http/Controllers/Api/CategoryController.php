<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all();

        return $this->sendResponse('Categories retrieved successfully.', $category);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->all());

        return $this->sendResponse('Category created successfully.', $category, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if (!$category) {
            return $this->sendError('Category not found.');
        }

        $category->update($request->all());

        return $this->sendResponse('Category updated successfully.', $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (!$category) {
            return $this->sendError('Category not found.');
        }

        $category->delete();

        return $this->sendResponse('Category deleted successfully.', null, 204);
    }
}
