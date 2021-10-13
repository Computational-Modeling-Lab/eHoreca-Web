<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
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

    public function getReportPhotosFilenamesAttribute($filenames)
    {
        $paths = [];
        // if no images are uploaded it could be null
        if (isset($filenames)) {
            foreach (json_decode($filenames) as $filename) {
                $paths[] = Storage::url($filename);
            }
        }
        return $paths;
    }

    public function getApprovedAttribute($approved)
    {
        if ($approved) {
            return 'Yes';
        }

        return 'No';
    }

    protected static function boot()
    {
        parent::boot();

        // everytime an eloquent query is performed for reports this query will be appended by default
        static::addGlobalScope(
            'location',
            function (\Illuminate\Database\Eloquent\Builder $builder) {
                $builder->select(\DB::raw('*, ST_AsText(location) AS location'));
            }
        );
    }
}
