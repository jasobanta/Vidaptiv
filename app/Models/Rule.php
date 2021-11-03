<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\RuleSegmentMetaDatas;
use App\Models\CarrierSetup;
use App\Models\CarrierRuleSegmentMetaDatas;
use Illuminate\Support\Facades\Log;
use App\Models\CarrierRuleMetaData;

class Rule extends Model {

    use HasFactory,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'priority',
    ];

    public function getStatusTitleAttribute($value) {
        return $value == 1 ? "Y" : "N";
    }

    public function getRuleSegments() {
        return $this->hasMany(RuleSegmentMetaDatas::class, 'rule_id', 'id')->where('status', 1)->whereNull('deleted_at');
    }

    public function ruleList($rule_type_id = 0) {
        try {
            $rows = Rule::select([
                        'id',
                        'name',
                        'rule_type_id',
                        'rules',
                        'priority',
                        'rule_fields',
                        'status',
                    ])
                    ->where('status', 1);
            if (!empty($rule_type_id)) {
                $rows = $rows->where('rule_type_id', $rule_type_id);
            }
            return $rows->orderBy('priority')->get()->collect()->toArray();
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: ruleList(): " . $e->getMessage());
            return [];
        }
    }

    public function ruleNameList($rule_type_id = 0, $is_ignore = '') {
        try {
            $rules = $this->ruleList($rule_type_id);
            if (!empty($rules)) {
                foreach ($rules as $rule) {
                    $rules_data[$rule['id']] = [
                        'rules' => $rule['rules'],
                        'name' => $rule['name'],
                        'rule_fields' => $rule['rule_fields'],
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: ruleNameList(): " . $e->getMessage());
        }
        return !empty($rules_data) ? $rules_data : [];
    }

    public function getCarrierRuleMetaDatas($carrier = '') {
        $compare_elements = $free_text_compare = [];
        try {
            if (!empty($carrier)) {
                $carrier_id = $carrier;
                if ($carrier <= 0) {
                    $carrier_id = CarrierSetup::select('id')->where('carrier_scac', $carrier)->where('status', 1)->pluck('id')->first();
                }

                $carrier_rules = CarrierRuleMetaData::select(['rule_id', 'compare_elements', 'is_free_text_compare'])
                        ->where('status', 1)
                        ->where('is_ignore', 0)
                        ->where('carrier_id', $carrier_id)
                        ->get();

                if (!empty($carrier_rules)) {
                    foreach ($carrier_rules as $rule) {
                        $compare_elements[$rule['rule_id']] = $rule['compare_elements'];
                        $free_text_compare[$rule['rule_id']] = $rule['is_free_text_compare'];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: getCarrierRuleMetaDatas(): " . $e->getMessage());
        }

        return [
            'compare_elements' => $compare_elements,
            'free_text_compare' => $free_text_compare,
        ];
    }

    public function defaultCompareElements($carrier = '') {
        $rules = [];
        try {
            if (!empty($carrier)) {
                $carrier_id = $carrier;
                if ($carrier <= 0) {
                    $carrier_id = CarrierSetup::select('id')->where('carrier_scac', $carrier)->where('status', 1)->pluck('id')->first();
                }

                $carrier_rules = CarrierRuleMetaData::select(['rule_id', 'compare_elements'])
                        ->where('status', 1)
                        ->where('is_ignore', 0)
                        ->where('carrier_id', $carrier_id)
                        ->get();

                if (!empty($carrier_rules)) {
                    foreach ($carrier_rules as $rule)
                        $compare_elements[$rule['rule_id']] = $rule['compare_elements'];
                }
            }

            $rows = self::select([
                        'id',
                        'default_compare_elements'
                    ])
                    ->where('status', 1)
                    ->where('default_compare_elements', '!=', '')
                    ->get();

            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $compare_elements_data = $row['default_compare_elements'];
                    if (!empty($compare_elements[$row['id']])) {
                        $compare_elements_data = $compare_elements[$row['id']];
                    }

                    $element_array = explode(',', $compare_elements_data);

                    if (!empty($element_array)) {
                        foreach ($element_array as $element_row) {
                            $sub_element_array = explode('.', $element_row);

                            if (!empty($sub_element_array)) {
                                $i = 0;
                                foreach ($sub_element_array as $data) {
                                    if (count($sub_element_array) == 1) {
                                        $rules[$row['id']][$sub_element_array[0]][] = '';
                                    }
                                    if ($i > 0) {
                                        $rules[$row['id']][$sub_element_array[0]][] = $data;
                                    }
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }

            return $rules;
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: defaultCompareElements(): " . $e->getMessage());
            return $rules;
        }
    }

    public function getDefaultRules($carrier_scac = '') {
        $carrier_rule_rows = self::getCarrierRuleMetaDatas($carrier_scac);
        $compare_elements = $carrier_rule_rows['compare_elements'];
        $free_text_compare = $carrier_rule_rows['free_text_compare'];
 
        $rulesFilters = [];
        $rawRules = self::where('status', 1)->where('rule_type_id', 1);

        if (!empty($carrier_scac)) {
            $carrier_id = CarrierSetup::select(['id'])->where('status', 1)->where('carrier_scac', $carrier_scac)->pluck('id')->first();

            if (!empty($carrier_id)) {
                $rawRules = $rawRules->whereNotIn('id', function($query) use ($carrier_id) {
                    $query->select('rule_id')
                            ->where('is_ignore', 1)
                            ->whereNull('deleted_at')
                            ->where('carrier_id', $carrier_id)
                            ->from('carrier_rule_meta_datas');
                });
            }
        }

        $rawRules = $rawRules->orderby('priority')->get();

        $return_data = [];
        if (!empty($rawRules)) {
            foreach ($rawRules as $row) {
                $return_data[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'rule_type_id' => $row['rule_type_id'],
                    'rules' => $row['rules'],
                    'priority' => $row['priority'],
                    'status' => $row['status'],
                    'is_free_text_compare' => !empty($free_text_compare[$row['id']]) ? $free_text_compare[$row['id']] : $row['is_free_text_compare'],
                    'default_compare_elements' => !empty($compare_elements[$row['id']]) ? $compare_elements[$row['id']] : $row['default_compare_elements'],
                    'rule_fields' => $row['rule_fields'],
                ];
            }
        }

        return $return_data;
    }

}
