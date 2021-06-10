<?php

namespace App\Http\Resources;

use App\Models\Villa;
use App\Models\Facility;
use App\Http\Resources\CategoryCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class VillaResource extends JsonResource
{
    private $message;

    public function __construct($resource, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->message = $message; 
    }

    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->loadMissing([
            'account'=> function ($query) {
                $query->withTrashed();
            },
        ]);
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
                'categories' => new CategoryCollection($this->categories),
                'supplier' => $this->when(auth()->user()->isSystemAdmin(), [
                    'company_name' => $this->account->company_name,
                    'account_status' => $this->when(!is_null($this->account->deleted_at), 'inactive'),
                    'account' => $this->when( is_null($this->account->deleted_at), route('accounts.show', $this->account->id)),
                    'villas' => $this->when(is_null($this->account->deleted_at), route('accounts.villas', $this->account->id)),
                ]),
                'deleted_at' => $this->when(!is_null($this->deleted_at), function(){
                    return $this->deleted_at->format('Y-m-d h:s');
                }),
            ]
        ];
    }
}
