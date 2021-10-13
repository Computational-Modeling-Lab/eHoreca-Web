<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plates',
        'type',
        'make',
        'year_first_license',
        'taxable_hp',
        'payload',
        'payload_unit',
        'municipality',
        'unity',
        'in_service',
    ];

    protected $guarded = [];

    protected $hidden = [
        'deleted_at'
    ];

    public function getInServiceAttribute($in_service)
    {
        return $in_service ? 'Yes': 'No';
    }
}
