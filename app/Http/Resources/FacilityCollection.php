<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Facility;

class FacilityCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "facility_count" => $this->collection->count(),
            'facilities' => Facility::group_by_type($this->collection),
        ];
    }
}
