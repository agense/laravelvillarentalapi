<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CityCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'city_count' => $this->collection->count(),
            'cities' => $this->collection->map(function($city, $index){
                return new CityResource($city);
            }),
        ];
    }
}
