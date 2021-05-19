<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\DateService;

class VillaPriceResource extends JsonResource
{
    private $price;
    private $message;

    public function __construct($resource, Array $priceData, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->priceData= $priceData; 
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
            ],
            "availability_period" => [
                "from" => $request->start_date ?? DateService::defaultPeriodStartDate(),
                "to" => $request->end_date ?? DateService::defaultPeriodEndDate(),
            ],
            "availability" => $this->priceData,
        ];
    }
}
