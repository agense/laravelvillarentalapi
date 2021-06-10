<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User; 

class UserResource extends JsonResource
{
    private $showAccount;
    private $message;

    public function __construct(User $resource, Bool $showAccount = true, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->showAccount = $showAccount;
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'account' => $this->when(($this->showAccount && $this->isClient()), route('accounts.show', $this->id)),
        ];
    }
}
