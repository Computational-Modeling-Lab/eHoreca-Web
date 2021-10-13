<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'plates' => $this->plates,
            'make' => $this->make,
            'first year of license' => $this->year_first_license,
            'horsepower' => "$this->taxable_hp",
            'payload' => "$this->payload",
            'payload_unit' => "$this->payload_unit",
            'municipality' => $this->municipality,
            'in service' => $this->in_service,
            'created at' => $this->created_at,
            'updated at' => $this->created_at,
        ];
    }
}
