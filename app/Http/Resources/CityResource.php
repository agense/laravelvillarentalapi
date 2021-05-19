<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'city' => [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'region' => $this->region->only('id','name', 'slug'),
                'villas_count' => $this->villas_count ?? 0,
            ]
        ];
    }
}
