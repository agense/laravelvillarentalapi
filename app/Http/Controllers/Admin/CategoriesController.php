<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;


class CategoriesController extends Controller
{
    public function __construct(){
        $this->middleware('can:manage-app')->except('index');
    }
    /**
     * Display a listing of the categories.
     * @return \App\Http\Resources\CategoryCollection
     */
    public function index()
    {
        Gate::authorize('access-admin');
        
        $categories = Category::withCount('villas')->orderBy('name')->get();
        return new CategoryCollection($categories);
    }

    /**
     * Store a newly created category in storage.
     * @param  \App\Http\Requests\CategoryRequest $request
     * @return \App\Http\Resources\CategoryResource
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->only('name'));
        return new CategoryResource($category, "Category created");
    }

    /**
     * Update the specified category in storage.
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @param  \App\Models\Category $category
     * @return \App\Http\Resources\CategoryResource
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
