<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bin extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = [
        'deleted_at'
    ];

    public function getLocationAttribute($location)
    {
        return \Helper::instance()->horeca_point_to_latlng($location);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    protected static function boot()
    {
        parent::boot();

        // everytime an eloquent query is performed for reports this query will be appended by default
        static::addGlobalScope(
            'location',
            function (\Illuminate\Database\Eloquent\Builder $builder) {
                $builder->select(\DB::Raw('*, ST_AsText(location) AS location'));
            }
        );
    }
}
