<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegionRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\RegionResource;
use App\Http\Resources\RegionCollection;

class RegionsController extends Controller
{
    public function __construct(){
        $this->middleware('can:manage-app')->except(['index', 'show']);
    }
    /**
     * Display a listing of the regions
     * @return \App\Http\Resources\RegionCollection
     */
    public function index()
    {
        Gate::authorize('access-admin');
        return new RegionCollection(Region::list());
    }

    /**
     * Store a newly created region in storage
     * @param  \App\Http\Requests\RegionRequest $request
     * @return \App\Http\Resources\RegionResource
     */
    public function store(RegionRequest $request)
    {
        $region = Region::create($request->only('name'));
        return new RegionResource($region, "Region created");
    }

    /**
     * Return region with cities
     * @param  \App\Models\Region  $Region
     * @return \App\Http\Resources\RegionResource
     */
    public function show(Region $region){
        Gate::authorize('access-admin');
        $region->load('cities');
        return new RegionResource($region);
    }

    /**
     * Update the specified region in storage
     * @param  \App\Http\Requests\RegionRequest $request
     * @param  \App\Models\Region $Region
     * @return \App\Http\Resources\RegionResource
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
        if(count($region->cities) > 0){
            abort(400, 'Regions that have associated cities cannot be deleted.');
        }
        $region->delete();
        return response()->json(['message' => 'Region deleted.']);
    }

}
