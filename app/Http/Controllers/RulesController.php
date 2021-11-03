<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rule;
use Illuminate\Support\Facades\Session;
use App\Models\CarrierSetup;
use App\Models\CarrierRuleMetaData;
use App\Models\RuleSegmentMetaDatas;

class RulesController extends Controller {

    public function index(Request $request) {
        $rows = Rule::select('*', 'status as status_title')->orderBy('priority')->get();
        return view('rules.list')->with('rows', $rows);
    }

    public function edit($id) {
        $row = Rule::where('id', $id)->first();
        return view('rules.form')->with('row', $row);
    }

    public function save(Request $request, $id = 0) {
        $request->validate([
            'priority' => 'required|numeric'
        ]);

        $data = [
            'status' => !empty($request->status) ? 1 : 0,
            'default_compare_elements' => !empty($request->default_compare_elements) ? $request->default_compare_elements : '',
            'name' => !empty($request->name) ? $request->name : '',
            'priority' => !empty($request->priority) ? $request->priority : 0,
            'is_free_text_compare' => !empty($request->is_free_text_compare) ? $request->is_free_text_compare : 0,
        ];

        if ($id > 0) {
            Rule::where('id', $id)->update($data);
            Session::flash('update_message', 'Rule data updated!');
        } else {
            Rule::create($data);
            Session::flash('add_message', 'New rule added!');
        }

        return redirect()->route('rule.list');
    }

    public function updatePriority(Request $request, $id, $priority) {
        try {
            $result = Rule::where('id', $id)->update(['priority' => $priority]);
            if ($result) {
                $rows = Rule::select(['id'])->whereNull('deleted_at')->orderBy('priority', 'ASC')->get();
                if (!empty($rows)) {
                    $i = 1;
                    foreach ($rows as $row) {
                        Rule::where('id', $row->id)->update(['priority' => $i]);
                        $i++;
                    }
                }
                return [
                    'success' => true,
                    'message' => "SUCCESS",
                ];
            }
            return [
                'success' => false,
                'message' => "ERROR",
            ];
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => "SERVER_ERROR",
            ];
        }
    }

    public function ruleCarrierList() {
        $rules_data = (new Rule())->ruleNameList(1);

        $rows = CarrierSetup::select([
                            'id',
                            'carrier_name',
                            'carrier_scac',
                        ])
                        ->where('status', 1)
                        ->whereNull('deleted_at')
                        ->orderBy('carrier_scac', 'ASC')
                        ->with('getCarrierIgnoreRules')
                        ->with('getCarrierCompareElementRules')
                        ->get()->collect()->toArray();

        return view('rules.rule_carrier_list')->with('rows', $rows)->with('rules_data', $rules_data);
    }

    public function ruleCarrierEdit($carrier_id) {
        $rules = Rule::select([
                            'id',
                            'name',
                            'rule_type_id',
                            'rules',
                            'priority',
                            'status',
                        ])
                        ->where('status', 1)
                        ->where('rule_type_id', 1)
                        ->orderBy('priority')->get()->collect()->toArray();


        $all_rules = CarrierRuleMetaData::where('status', 1)->where('carrier_id', $carrier_id)
                        ->get()->collect()->toArray();

        $row = CarrierSetup::where('id', $carrier_id)->first();
        return view('rules.rule_carrier_form')->with('row', $row)->with('rules', $rules)->with('all_rules', $all_rules);
    }

    public function ruleCarrieAddIgnore(Request $request, $carrier_id = 0) {
        $request->validate([
            'rule_ids' => 'required'
                ], [
            'rule_ids.required' => 'Select at least one rule from default list,',
        ]);

        try {
            $meta_data_obj = new CarrierRuleMetaData();
            if (!empty($carrier_id) && !empty($request->rule_ids)) {
                foreach ($request->rule_ids as $rule_id) {
                    $insert_data[] = [
                        'status' => 1,
                        'rule_id' => $rule_id,
                        'carrier_id' => $carrier_id,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ];
                }

                $meta_data_obj->insert($insert_data);

                Session::flash('success_message', 'Carrier rule added in ignore list.');
                return redirect()->route('rule.carrier.edit', $carrier_id);
            }
            Session::flash('error_message', 'ERROR');
            return redirect()->route('rule.carrier.edit', $carrier_id);
        } catch (\Exception $ex) {
            Session::flash('error_message', 'SERVER_ERROR');
            return redirect()->route('rule.carrier.edit', $carrier_id);
        }
    }

    public function ruleCarrieDeleteIgnore(Request $request, $carrier_id = 0) {
        $request->validate([
            'rule_ids' => 'required'
                ], [
            'rule_ids.required' => 'Select at least one rule from ignore list.',
        ]);

        try {
            $meta_data_obj = new CarrierRuleMetaData();
            if (!empty($carrier_id) && !empty($request->rule_ids)) {

                $meta_data_obj->where('carrier_id', $carrier_id)
                        ->where('status', 1)
                        ->whereIn('rule_id', $request->rule_ids)
                        ->update(['status' => 0]);

                Session::flash('success_message', 'Carrier rule added in ignore list.');
                return redirect()->route('rule.carrier.edit', $carrier_id);
            }


            Session::flash('error_message', 'ERROR');
            return redirect()->route('rule.carrier.edit', $carrier_id);
        } catch (\Exception $ex) {
            Session::flash('error_message', 'SERVER_ERROR');
            return redirect()->route('rule.carrier.edit', $carrier_id);
        }
    }

    public function ruleSegmentList() {
        $rules_data = (new Rule())->ruleNameList(1);

        $rows = Rule::select([
                            'id',
                            'rules',
                            'name',
                        ])
                        ->where('status', 1)
                        ->whereNull('deleted_at')
                        ->whereIn('id', function($query) {
                            $query->select('rule_id')
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->from('rule_segment_meta_datas');
                        })
                        ->with('getRuleSegments')
                        ->get()->collect()->toArray();

        return view('rules.rule_segment_list')->with('rows', $rows)->with('rules_data', $rules_data);
    }

    public function ruleSegmentAdd(Request $request) {

        $request->validate([
            'rule_fields' => 'required'
                ], [
            'rule_fields.required' => 'Select at least one segment item.',
        ]);

        $rule_id = $request->rule_id;

        try {
            $meta_data_obj = new RuleSegmentMetaDatas();
            if (!empty($rule_id) && !empty($request->rule_fields)) {

                $meta_data_obj->where('rule_id', $rule_id)->update(['status' => 0, 'deleted_at' => now()]);

                foreach ($request->rule_fields as $field_id) {
                    $insert_data[] = [
                        'status' => 1,
                        'rule_id' => $rule_id,
                        'segment_field_id' => $field_id,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ];
                }

                $meta_data_obj->insert($insert_data);

                Session::flash('success_message', 'Rule fields added in segment list.');
                return redirect()->route('rule.segment.list');
            }
            Session::flash('error_message', 'ERROR');
            return redirect()->route('rule.segment.list');
        } catch (\Exception $ex) {
            Session::flash('error_message', 'SERVER_ERROR');
            return redirect()->route('rule.segment.list');
        }
    }

    public function ruleSegmentDelete($rule_id) {
        try {
            RuleSegmentMetaDatas::where('rule_id', $rule_id)->update(['status' => 0, 'deleted_at' => now()]);
            Session::flash('success_message', 'Rule segment fields deleted.');
            return redirect()->route('rule.segment.list');
        } catch (\Exception $ex) {
            Session::flash('error_message', 'ERROR');
            return redirect()->route('rule.segment.list');
        }
    }

    public function ruleCarrierUpdate(Request $request, $carrier_id = 0) {
        $meta_data_obj = new CarrierRuleMetaData();

        if (!empty($request->is_ignore)) {
            foreach ($request->is_ignore as $key => $val) {
                $insert_data[] = [
                    'rule_id' => $key,
                    'is_ignore' => 1,
                    'compare_elements' => NULL,
                    'status' => 1,
                    'carrier_id' => $carrier_id,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }
        }

        if (!empty($request->compare_element)) {

            foreach ($request->compare_element as $key => $val) {
                $insert_data[] = [
                    'rule_id' => $key,
                    'is_ignore' => 0,
                    'compare_elements' => isset($val[0]) ? $val[0] : NULL,
                    'is_free_text_compare' => !empty($request->is_free_text_compare[$key]) ? 1 : 0,
                    'status' => 1,
                    'carrier_id' => $carrier_id,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }
        }

        if (!empty($insert_data)) {
            $meta_data_obj->where('carrier_id', $carrier_id)->update(['status' => 0, 'deleted_at' => now()]);

            $meta_data_obj = new CarrierRuleMetaData();
            $meta_data_obj->insert($insert_data);

            Session::flash('success_message', 'Carrier rule updated successfully.');
            return redirect()->route('rule.carrier.edit', $carrier_id);
        }

        Session::flash('error_message', 'Carrier rule not updated.');
        return redirect()->route('rule.carrier.edit', $carrier_id);
    }

}
