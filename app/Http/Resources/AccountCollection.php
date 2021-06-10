<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\AccountResource;

class AccountCollection extends ResourceCollection
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
            "account_count" => $this->collection->count(),
            "accounts" => $this->collection->map(function($item){
                return new AccountResource($item, false);
            })
        ];
    }
}
