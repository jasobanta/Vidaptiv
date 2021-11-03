<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountryDocument extends Model {

    use HasFactory,
        SoftDeletes;

    public function getCountryDocumentsByName($country_name = '') {
        try {
            return $this->select(['documents'])
                            ->where('status', 1)
                            ->where('country_name', $country_name)
                            ->pluck('documents')->first();
        } catch (\Exception $e) {
            return '';
        }
    }

}
