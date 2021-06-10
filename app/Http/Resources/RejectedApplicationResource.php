<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RejectedApplicationResource extends JsonResource
{
    private $isSingle;
    private $message;

    public function __construct($resource, Bool $isSingle = true, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->isSingle = $isSingle;
        $this->message = $message;
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
            "id"=> $this->id,
            "account_type"=> $this->account_type,
            "company_name"=> $this->company_name,
            "company_registration_number" => $this->when($this->isSingle, $this->company_registration_number),
            "company_owner_name"=> $this->when($this->isSingle, $this->company_owner_name),
            "company_email"=> $this->when($this->isSingle, $this->company_email),
            "company_phone"=> $this->when($this->isSingle, $this->company_phone),
            "company_website"=> $this->when($this->isSingle, $this->company_website),
            "company_address"=> $this->when($this->isSingle, $this->company_address),
            "company_city"=> $this->when($this->isSingle, $this->company_city),
            "company_country"=> $this->company_country,
            "applied_at"=> $this->applied_at->format('Y-m-d h:s'),
            "rejected_at"=> $this->rejected_at->format('Y-m-d h:s'),
            "reason"=> $this->when($this->isSingle, $this->reason),
            "url" => $this->when(!$this->isSingle, route('applications.rejected.show', $this->id)),
        ];
    }
}
