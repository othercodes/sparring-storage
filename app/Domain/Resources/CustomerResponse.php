<?php

namespace App\Domain\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'parent' => $this->parent->id
        ];
    }
}
