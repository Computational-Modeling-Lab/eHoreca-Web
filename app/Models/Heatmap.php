<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Heatmap extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function setValidFromAttribute($value)
    {
        $this->attributes['valid_from'] =  Carbon::parse($value);
    }

    public function setValidToAttribute($value)
    {
        $this->attributes['valid_to'] =  Carbon::parse($value);
    }
}
