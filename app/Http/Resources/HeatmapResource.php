<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class HeatmapResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::find($this->user_id);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created by' => $user !== null ?  "$user->name $user->surname" : 'User deleted',
            'valid from' => date('yy-m-d', strtotime($this->valid_from)),
            'valid to' => date('yy-m-d',  strtotime($this->valid_to)),
            'created at' => $this->created_at,
            'updated at' => $this->updated_at,
        ];
    }
}
