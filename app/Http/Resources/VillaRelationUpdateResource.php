<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\VillaImageResource;
use Illuminate\Database\Eloquent\Collection;

class VillaRelationUpdateResource extends JsonResource
{
    private $message;
    private $modified;
    private $baseUrl;
    private $modification;

    public function __construct($resource, Collection $modified, String $relation, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->relation = $relation;
        $this->modified = $modified;
        $this->message = $message; 
        $this->baseUrl = config('app.url');
        $this->modification = request()->isMethod('delete') ? "removed" : "added";
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = ($this->relation !== 'images') ? $this->modified->values() : VillaImageResource::collection($this->modified);
        return [
            "message" => $this->when($this->message !== null, $this->message),
            "{$this->modification}_{$this->relation}_count" => $this->modified->count(),
            "{$this->modification}_{$this->relation}" => $data,
            "villa" => [
                "id" => $this->id,
                "name" => $this->name,
                "url" => $this->baseUrl."/api/admin/villas/".$this->id,
            ]
        ];
    }
}
