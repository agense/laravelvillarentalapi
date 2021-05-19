<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Villa;

class VillaCollection extends ResourceCollection
{
    private $baseUrl;

    public function __construct($resource) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->baseUrl = config('app.url');
    }
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $collection = $this->collection->map(function($item, $key){
            $newItem = [];
            $newItem['id'] = $item->id;
            $newItem['name'] = $item->name;
            $newItem['slug'] = $item->slug;
            $newItem['area'] = $item->area.$item->area_measurement;
            $newItem['capacity'] = $item->capacity;
            $newItem['bedrooms'] = $item->bedrooms;
            $newItem['region'] = $item->city->region->name;
            $newItem['city'] = $item->city->name;
            $newItem['image'] = $item->images->count() > 0 ? new VillaImageResource($item->images->first()) : null;
            $newItem['link'] = [
                'method' => 'GET',
                'url' => $this->baseUrl."/api/admin/villas/".$item->id,
            ];
            return $newItem;
        });

        if($collection->isNotEmpty()){
            return [
                'villas_count' => $collection->count(),
                'area_measurement_unit' => Villa::getAreaMeasurementUnit(),
                'villas' => $collection,
            ];
        }else{
            return [
                'message' => "No villas were found",
                'villas_count' => '0',
            ];
        }
        
    }
}
