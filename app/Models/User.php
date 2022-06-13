<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'password',
        'role',
        'details',
        'token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'tokenexpiration',
        'token',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'timestamp',
    ];

    public function heatmaps()
    {
        return $this->hasMany(Heatmap::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function getRememberTokenName()
    {
        return 'token';
    }
}
