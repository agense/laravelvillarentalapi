<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;


class CategoriesController extends Controller
{
    /**
     * Display a listing of the categories.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::withCount('villas')->orderBy('name')->get();
        return new CategoryCollection($categories);
    }

    /**
     * Store a newly created category in storage.
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->only('name'));
        return new CategoryResource($category, "Category created");
    }

    /**
     * Update the specified category in storage.
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->name = $request->name;
        $category->save();
        return new CategoryResource($category, "Category updated");
    }

    /**
     * Remove the specified category from storage.
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->villas()->detach();
        $category->delete();
        return response()->json(["message" => "Category deleted."]);
    }
}
