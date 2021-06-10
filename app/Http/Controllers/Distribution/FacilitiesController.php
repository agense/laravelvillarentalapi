<?php

namespace App\Http\Controllers\Distribution;

use App\Models\Facility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FacilityCollection;

class FacilitiesController extends Controller
{
    /**
     * Display a listing of facilities
     * @return \App\Http\Resources\FacilityCollection
     */
    public function index()
    {
         $facilities = Facility::orderBy('name')->get();
         return new FacilityCollection($facilities);
    }
}
