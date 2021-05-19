<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegionRequest;
use App\Http\Resources\RegionResource;
use App\Http\Resources\RegionCollection;
use App\Models\Region;

class RegionsController extends Controller
{
    /**
     * Display a listing of the regions
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new RegionCollection(Region::list());
    }

    /**
     * Store a newly created region in storage
     * @param  \App\Http\Requests\RegionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionRequest $request)
    {
        $region = Region::create($request->only('name'));
        return new RegionResource($region, "Region created");
    }

    /**
     * Update the specified region in storage
     * @param  \App\Http\Requests\RegionRequest $request
     * @param  \App\Models\Region $Region
     * @return \Illuminate\Http\Response
     */
    public function update(RegionRequest $request, Region $region)
    {
        $region->name = $request->name;
        $region->save();
        return new RegionResource($region, "Region updated");
    }

    /**
     * Remove the specified region from storage
     * @param  \App\Models\Region $Region
     * @return \Illuminate\Http\Response
     */
    public function destroy(Region $region)
    {
        $region->delete();
        return response()->json(['message' => 'Region deleted.']);
    }

    /**
     * Return region with cities
     * @param  \App\Models\Region  $Region
     * @return \Illuminate\Http\Response
     */
    public function getCities(Region $region){
        $region->load('cities');
        return new RegionResource($region);
    }

}
