<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\RejectedApplicationResource;

class RejectedApplicationCollection extends ResourceCollection
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
            "application_count" => $this->collection->count(),
            "applications" => $this->collection->map(function($item){
                return new RejectedApplicationResource($item, false);
            })
        ];
    }
}
