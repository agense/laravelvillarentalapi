<?php

namespace App\Http\Controllers\Distribution;

use App\Models\City;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityCollection;
use App\Http\Resources\RegionResource;
use App\Http\Resources\RegionCollection;

class LocationsController extends Controller
{
    /**
     * Display a listing of the cities.
     * @return App\Http\Resources\CityCollection
     */
    public function getCities()
    {
        return new CityCollection(City::list());
    }

    /**
     * Display a listing of the regions
     * @return App\Http\Resources\RegionCollection
     */
    public function getRegions()
    {
        return new RegionCollection(Region::list());
    }

    /**
     * Return region with cities
     * @param  \App\Models\Region  $Region
     * @return \App\Http\Resources\RegionResource
     */
    public function getRegion(Region $region){
        $region->load('cities');
        return new RegionResource($region);
    }
}
