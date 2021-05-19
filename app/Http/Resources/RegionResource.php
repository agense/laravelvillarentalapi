<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class RegionResource extends JsonResource
{
    private $message;

    public function __construct($resource, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->message = $message;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'message' => $this->when($this->message !== null, $this->message),
            "region" => [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'cities_count' => $this->cities_count ?? 0,
                'villas_count' => $this->villas_count ?? 0,
                'cities' => $this->whenLoaded('cities', function () {
                    return self::mapCities($this->cities);
                }),
            ]
        ];
    }
    
    private function mapCities($cities){
        return $cities->map(function($city, $index){
            return collect($city)->except('region', 'region_id');
        });
    }

}
