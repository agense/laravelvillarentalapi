<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'category' => [
                "id" => $this->id,
                "name" => $this->name,
                "slug" => $this->slug,
                "villa_count" => $this->whenLoaded('villa_count'),
            ]
        ];
    }
}
