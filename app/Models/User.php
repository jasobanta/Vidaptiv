<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable {

    use HasFactory,
        Notifiable,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_admin',
        'active',
        'name',
        'email',
        'password',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getActiveTitleAttribute($value) {
        return $value == 1 ? "Y" : "N";
    }

    public function getIsAdminTitleAttribute($value) {
        return $value == 1 ? "Y" : "N";
    }

    public function getIsAllCarriersTitleAttribute($value) {
        return $value == 1 ? "Y" : "N";
    }

    public function userCarriers() {
        return $this->hasMany(UserCarrier::class, 'user_id');
    }

}
