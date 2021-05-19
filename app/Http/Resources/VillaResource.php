<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Facility;
use App\Models\Villa;

class VillaResource extends JsonResource
{
    private $message;
    private $baseUrl;

    public function __construct($resource, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->message = $message; 
        $this->baseUrl = config('app.url');
    }

    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'message' => $this->when($this->message !== null, $this->message),
            'villa' => [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'area' => $this->area,
                'area_measurement_unit' => Villa::getAreaMeasurementUnit(),
                'capacity' => $this->capacity,
                'bedrooms' => $this->bedrooms,
                'bathrooms' => $this->bathrooms,
                'region' => $this->city->region->only('name', 'slug', 'id'),
                'city' => $this->city->only('name', 'slug', 'id'),
                'facilities' => [
                    'count' => $this->facilities_count ? $this->facilities_count : 0,
                    'list' => Facility::group_by_type($this->facilities),
                ],
                'images' => $this->images->count() > 0 ? VillaImageResource::collection($this->images) : [],
                'description' => $this->description,
                'categories' => CategoryResource::collection($this->categories),
                'links' => [
                    'all_villas' => 
                        [
                            'method' => 'GET',
                            'url' => $this->baseUrl."/api/admin/villas/",
                        ],
                ]
            ]
        ];
    }
}
