<?php

namespace App\Http\Controllers\Distribution;

use App\Models\Villa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VillaResource;
use App\Http\Resources\VillaCollection;

class VillasController extends Controller
{
    /**
     * Display a listing of all villas
     * @return \App\Http\Resources\VillaCollection
     */
    public function index()
    {
        $villas = Villa::all();
        return new VillaCollection($villas);
    }

    /**
     * Display the specified villa
     * @param  int $id
     * @return \App\Http\Resources\VillaResource
     */
    public function show($id)
    {
        $villa = Villa::WithFullData()->findOrFail($id);
        return new VillaResource($villa);
    }

   
}
