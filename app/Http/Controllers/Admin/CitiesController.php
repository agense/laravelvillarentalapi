<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\CityCollection;
use App\Models\City;


class CitiesController extends Controller
{
    /**
     * Display a listing of the cities.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new CityCollection(City::list());
    }

    /**
     * Store a newly created city in storage
     * @param  \App\Http\Requests\CityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
        $city = City::create($request->only('name', 'region_id'));
        return  new CityResource($city, "City created");
    }

    /**
     * Update the specified city in storage.
     * @param  \App\Http\Requests\CityRequest  $request
     * @param  \App\Models\City  $City
     * @return \Illuminate\Http\Response
     */
    public function update(CityRequest $request, City $city)
    {
        $city->name = $request->name;
        $city->region_id = $request->region_id;
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
        $city->delete();
        return response()->json(['message' => 'City deleted.']);
    }
}
