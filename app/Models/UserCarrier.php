<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CarrierSetup;

class UserCarrier extends Model {

    use HasFactory,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'carrier_id',
        'status'
    ];

    public function getCarrierIds($user_id) {
        try {
            return $this->select(['carrier_id'])->where('user_id', $user_id)->get()->pluck('carrier_id')->collect()->toArray();
        } catch (\Exception $ex) {
            return [];
        }
    }

    public function getCarriers() {
        return $this->hasMany(CarrierSetup::class, 'id', 'carrier_id');
    }

}
