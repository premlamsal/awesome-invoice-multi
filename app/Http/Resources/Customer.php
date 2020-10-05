<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
          return[
            'id'=>$this->id,
            'name'=> $this->name,
            'address'=> $this->address,
            'phone'=> $this->phone,
            'details'=> $this->details,
            'updated_at'=>$this->updated_at,
        ];
    }
}
