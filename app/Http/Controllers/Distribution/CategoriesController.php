<?php

namespace App\Http\Controllers\Distribution;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the categories.
     * @return \App\Http\Resources\CategoryCollection
     */
    public function index()
    {
        $categories = Category::withCount('villas')->orderBy('name')->get();
        return new CategoryCollection($categories);
    }
}
