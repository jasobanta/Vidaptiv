<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edi_data extends Model {

    use HasFactory;

    protected $guarded = ['id'];

    public function getOwnerEmailTitleAttribute($value) {
        $v = explode('@', $value);
        return ucwords(str_replace('.', ' ', $v[0]));
    }

    public function getMetadata() {
        return $this->hasMany(Edi_meta_data::class, 'edi_data_id');
    }

    public function getReceivedDate() {
        return $this->hasOne(self::class, 'id', 'compared_with');
    }

    public function getFfNumberAttribute($value) {
        return str_replace(['BDP', 'bdp'], '', $value);
    }

}
