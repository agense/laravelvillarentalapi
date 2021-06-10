<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\DateService;

class VillaAvailabilityResource extends JsonResource
{
    private $availability;
    private $message;

    public function __construct($resource, Array $availabilityData, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->availabilityData = $availabilityData; 
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
            "villa" => [
                "id" => $this->id,
                "name" => $this->name,
                "url" => route('villas.show', $this->id),
            ],
            "availability_period" => [
                "from" => $request->start_date ?? DateService::defaultPeriodStartDate(),
                "to" => $request->end_date ?? DateService::defaultPeriodEndDate(),
            ],
            "availability" => $this->availabilityData,
        ];
    }
}
