<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WProducer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'join_pin',
        'title',
        'contact_telephone',
        'contact_name',
        'location',
        'users',
        'bins',
        'description',
        'is_approved',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'join_pin',
        'deleted_at'
    ];

    public function getLocationAttribute($location)
    {
        return \Helper::instance()->horeca_point_to_latlng($location);
    }

    public function getUsersAttribute($users)
    {
        return json_decode($users);
    }

    public function getBinsAttribute($bins)
    {
        return json_decode($bins);
    }

    public function getIsApprovedAttribute($is_approved)
    {
        return $is_approved ? 'Yes': 'No';
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
