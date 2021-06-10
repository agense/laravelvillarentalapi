<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\CityCollection;


class CitiesController extends Controller
{
    public function __construct(){
        $this->middleware('can:manage-app')->except('index');
    }
    /**
     * Display a listing of the cities.
     * @return \App\Http\Resources\CityCollection
     */
    public function index()
    {
        Gate::authorize('access-admin');
        return new CityCollection(City::list());
    }

    /**
     * Store a newly created city in storage
     * @param  \App\Http\Requests\CityRequest  $request
     * @return \App\Http\Resources\CityResource
     */
    public function store(CityRequest $request)
    {
        $city = City::create($request->only('name', 'region_id'));
        return new CityResource($city, "City created");
    }

    /**
     * Update the specified city in storage.
     * @param  \App\Http\Requests\CityRequest  $request
     * @param  \App\Models\City  $City
     * @return \App\Http\Resources\CityResource
     */
    public function update(CityRequest $request, City $city)
    {
        $city->fill($request->only('name', 'region_id'));
        $city->save();
        return  new CityResource($city, "City updated");
    }

    /**
     * Remove the specified city from storage.
     * @param  \App\Models\City $City
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        if(count($city->villas) > 0){
            abort(400, 'Cities that have villas cannot be deleted. Delete villas first or ressign them to another location first');
        }
        $city->delete();
        return response()->json(['message' => 'City deleted.']);
    }
}
