<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarrierRuleMetaData extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'carrier_rule_meta_datas';
    protected $guarded = [
        'id'
    ];

}
