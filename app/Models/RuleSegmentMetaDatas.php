<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuleSegmentMetaDatas extends Model {

    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'rule_id',
        'segment_field_id',
        'status',
    ];

    public function rulesSegmentMetaData() {
        $rules = [];
        try {
            $rows = self::select([
                        'rule_id',
                        'segment_field_id'
                    ])
                    ->where('status', 1)
                    ->get();

            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $rules[$row['rule_id']][] = $row['segment_field_id'];
                }
            }
            return $rules;
        } catch (\Exception $ex) {
            return $rules;
        }
    }

}
