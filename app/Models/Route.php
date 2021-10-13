<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // protected $casts = [
    //     'bins' => 'object',
    // ];

    protected $hidden = [
        'deleted_at'
    ];

    public function getBinsAttribute($bins)
    {
        return json_decode($bins);
    }

    public function getOutcomeAttribute($outcome)
    {
        return json_decode($outcome);
    }
}
