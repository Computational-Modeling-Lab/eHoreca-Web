<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'bin' => $this->bin_id,
            'created by' => $user !== null ? "$user->name $user->surname" : 'User deleted',
            'location' => $this->location,
            'location accuracy' => "$this->location_accuracy m.",
            'issue' => $this->issue,
            'comment' => $this->comment,
            'report photos' => $this->report_photos_filenames,
            'approved' => $this->approved,
            'created at' => $this->created_at,
            'updated at' => $this->updated_at,
        ];
    }
}
