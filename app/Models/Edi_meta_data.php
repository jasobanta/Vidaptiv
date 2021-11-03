<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edi_meta_data extends Model {

    use HasFactory;

    protected $guarded = ['id'];

    public function acceptedRuleIds($edi_data_id = 0) {
        $return_data = [];
        $rows = self::select('rules_id')->where('edi_data_id', $edi_data_id)->where('is_accepted', 1)->get();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $return_data[] = $row['rules_id'];
            }
        }
        return $return_data;
    }

}
