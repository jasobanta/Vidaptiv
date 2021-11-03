<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model {

    use HasFactory,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    public function getCountryCodeList() {
        $list = [];
        try {
            $rows = $this->select(['id', 'country_code'])
                    ->where('status', 1)
                    ->get();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $list[$row['id']] = $row['country_code'];
                }
            }

            return $list;
        } catch (\Exception $e) {
            return $list;
        }
    }

}
