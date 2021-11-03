<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\EDI\Reader;
use App\Models\Edi_data;
use App\Models\Rule;
use App\Models\Edi_meta_data;
use App\Models\CarrierSetup;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\EdiSendCompareEmail;
use App\Models\CountryDocument;
use App\Models\RuleSegmentMetaDatas;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\EdiEmailController;
use App\Http\Controllers\library\EdiReaderController;
use App\Models\CarrierRuleSegmentMetaDatas;
use App\Models\Country;
use App\Models\EdiTitle;

class EdiController extends Controller {

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request) {
//
    }

    private $default_compare_elements;
    private $segment_rules_meta_data;
    private $free_text_rule_ids;

    public function fileMoveToArchive($folder = 'ARCHIVE', $rename = false, $file_full_path = '') {
        try {
            if (Storage::exists($file_full_path)) {
                $file_name = basename($file_full_path);

                if ($rename == true) {
                    $file_full_path = str_replace($file, date("Y_m_d_h_i_s") . "__" . $file_name, $file_full_path);
                }

                $new_file_path = $folder . "/" . $file_full_path;

                if (Storage::exists($new_file_path)) {
                    $new_file_path = str_replace($file_name, rand() . '_' . $file_name, $new_file_path);
                }
                return Storage::move($file_full_path, $new_file_path);
            }
            return false;
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: fileMoveToArchive(): " . $e->getMessage() . ' Line NO ' . $e->getLine());
            return false;
        }
    }

    public function processEdi(Request $request, $type) {
        $in_or_out = ($type === 'incoming') ? 1 : 0;

        $country_code_list = array_flip((new Country())->getCountryCodeList());

        $Edi_data = new Edi_data;
        $typePass = ($type === 'incoming') ? 'edi' : 'meridian';
        $return_data = 'Start processing ' . $type . ' files.<br/>';
        $return_data .='###############################<br/>';
        $files = Storage::allFiles($typePass);

        foreach ($files as $file) {
            $path = Storage::path($file);
            $reader = new EdiReaderController($file);

            $carrier = !empty($reader->getCarrier()) ? trim($reader->getCarrier()) : '';

            if (empty($carrier)) { // file is moved on error folder if UNB/date not found
//$this->fileMoveToArchive('ERROR', true, $file);
                continue;
            }

            $dtm = $reader->getEdiDate();
            $bn = $reader->getFirstStrValue('RFF+BN', 3);
            $bm = $reader->getFirstStrValue('RFF+BM', 3);
            $ff = str_replace(['BDP', 'bdp'], '', $reader->getFirstStrValue('RFF+FF', 3));

            $email_name = 'Owner missing';
            $comm = $reader->getComValue('COM');

            if ($typePass == 'edi') { //carrier details start with CA
                $email_name = $comm['carrier']['email_name'];
                $email = $comm['carrier']['email'];
            } else {
                $email_name = $comm['owner']['email_name'];
                $email = $comm['owner']['email'];
            }

            $country_code = $reader->getCountryCode('NAD+HI');
            $country_id = !empty($country_code_list[$country_code]) ? $country_code_list[$country_code] : 0;

            try {
                $filenameandpath = str_replace(storage_path('app'), '', $path);
                $filenameandpath = str_replace("/\\", '/', '\ARCHIVE/' . $filenameandpath);

                $edi_data = $Edi_data->firstOrCreate(
                        [
                    'data' => trim($filenameandpath),
                    'booking_no' => trim($bn),
                    'ff_no' => trim($ff),
                    'bn_no' => trim($bm),
                    'in_or_out' => trim($in_or_out),
                        ]
                        , [
                    'owner_email' => trim($email),
                    'owner_name' => trim($email_name),
                    'country_code' => trim($country_code),
                    'country_id' => trim($country_id),
//			'booking_no' => $bn,
//			'ff_no' => $ff,
//			'bn_no' => $bn,
                    'carrier' => strtoupper(trim($carrier)),
//			'data' => $filenameandpath,
                    'status' => 0,
                    'dtm' => !empty(trim($dtm)) ? trim($dtm) : '',
                ]);

                if (!empty($edi_data)) {
                    $this->fileMoveToArchive('ARCHIVE', false, $file);
                }

                $return_data .="Content of File " . basename($path) . " processing<br/><pre>";
                $return_data .="<br/>Content End of File $path process complete<br/></pre>";
            } catch (\Exception $e) {
                Log::info("SERVER_ERROR: processEdi(): " . $e->getMessage() . ' Line NO ' . $e->getLine());
                $return_data .= 'ERROR: ' . basename($path) . '<br/>';
            }
        }

        if ($request->formate == 'prity') {
            echo!empty($return_data) ? $return_data : "";
        } else {
            return [
                'success' => true,
                'message' => !empty($return_data) ? $return_data : '',
            ];
        }
    }

    public function CompareEdi(Request $request) {
        $edi_email_obj = new EdiSendCompareEmail();
        $edi_datas = Edi_data::where('status', 0)->where('compared_with', 0)->where('in_or_out', 0)->get();
//$edi_datas = Edi_data::where('id', 1)->get(); //for testing only

        $active_carriers = [];
        $carrier_rows = (new CarrierSetup())->getCarrierList();
        if ($carrier_rows) {
            foreach ($carrier_rows as $carrier) {
                if ($carrier['status'] == 1) {
                    $active_carriers[] = strtoupper($carrier['carrier_scac']);
                }
            }
        }

        $return_data = '';
        foreach ($edi_datas as $data) {
            try {
                $compare_to = Edi_data::where('status', 1)
                        ->where('in_or_out', 1)
                        ->where('booking_no', $data->booking_no)
                        ->where('id', '!=', $data->id)
                        ->where('carrier', $data->carrier)
                        ->where('compared_with', 0)
                        ->Orwhere('ff_no', $data->ff_no)
                        ->where('id', '!=', $data->id)
                        ->where('in_or_out', 1)
                        ->where('carrier', $data->carrier)
                        ->where('compared_with', 0);

                $compare_to_data = $compare_to->first();

                $sent_date = $data->dtm;
                $diff_seconds = 0;

                if (!empty($compare_to_data)) {
                    $incomingEdi = new Reader(storage_path('app') . $compare_to_data->data);
                    $incomingEdidata = $incomingEdi->getParsedFile();
                    $bm = $incomingEdi->readEdiDataValue(['RFF', ['1.0' => 'BM']], 1, 1);
                    $received_date = $compare_to_data->dtm;

                    if (!empty($received_date) && !empty($sent_date)) {
                        $diff_seconds = strtotime($received_date) - strtotime($sent_date);
                    }

                    $return_data .= "id=" . $data->id . ' Edi Data outgoing ' . $data->id . '::' . basename($data->data) . ' will be compared to Incoming Edi ' . $compare_to_data->id . '::' . basename($compare_to_data->data) . '<br/> ';
                    $return_data .= 'start processing' . basename($data->data);
                    $return_data .= "<br/>";

                    $data->received_date = $received_date;
                    $data->diff_seconds = $diff_seconds;
                    $data->bn_no = $bm;
                    $data->status = 1;
                    $data->compared_with = $compare_to_data->id;
                    $data->compared_at = date('Y-m-d H:i:s', strtotime(now()));
                    $data->save();

                    $compare_to_data->received_date = $sent_date;
                    $data->diff_seconds = $diff_seconds;
                    $compare_to_data->compared_with = $data->id;
                    $compare_to_data->status = 1;
                    $compare_to_data->compared_at = date('Y-m-d H:i:s', strtotime(now()));
                    $compare_to_data->save();

                    if (config('app.send_compare_edi_email') && in_array(strtoupper($data->carrier), $active_carriers)) {
                        $edi_email_obj->sendCompareEdiEmail($data['id']);
                        $return_data .= 'Compare Edi Email sent for ' . $data->id . '::' . basename($data->data) . '<br/>';
                    }
                } else {
                    $return_data .= 'There is nothing to compare with ' . $data->id . '::' . basename($data->data) . '<br/>';
                }
            } catch (\Exception $e) {
                Log::info("SERVER_ERROR: CompareEdi(): " . $e->getMessage());
                $return_data .= 'ERROR: ' . basename($data->data) . '<br/>';
            }
        }

        if ($request->formate == 'prity') {
            echo!empty($return_data) ? $return_data : 'There is nothing to compare.';
        } else {
            return [
                'success' => true,
                'message' => !empty($return_data) ? $return_data : 'There is nothing to compare.',
            ];
        }
    }

    public function showfile(Request $request) {
        $type = $request->input('type');
        $id = $request->input('id');
        $file = $request->input('file');

        $data = Edi_data::find($id);
        if ($type === 'diff') {
            $contents = $data->diff;
        } else {
            $contents = 'File not found.';
            if (Storage::disk('local')->exists($data->data)) {
                $contents = Storage::disk('local')->get($data->data);
            }
        }
        return view('showfile')->with('contents', $contents)->with('type', $type)->with('edi', $data)->with('file',$file);
    }

    public function getEdiCountryName($segments = []) {
        try {
            if (!empty($segments)) {
                $segment_data = end($segments);
                return end($segment_data);
            }
            return '';
        } catch (Exception $ex) {
            return '';
        }
    }

    public function array_change_value_case($array = [], $case = '') {
        if (!empty($array)) {
            $narray = [];
            foreach ($array as $key => $value) {
                $narray[$key] = ($case == CASE_UPPER ? strtoupper($value) : strtolower($value));
            }
            return $narray;
        }
        return $array;
    }

    public function removeSpecialCharacters($word = '') {
        return preg_replace('/(^([^a-zA-Z0-9])*|([^a-zA-Z0-9])*$)/', '', trim($word));
        /* try {
          $ignore_strings = ["?", "?'", "?:", ";", ".", "/", ",", "@"];
          $new_word = strtoupper(trim($word));

          if (!empty($ignore_strings)) {
          foreach ($ignore_strings as $ignore_str) {
          $new_word = rtrim(ltrim($new_word, $ignore_str), $ignore_str);
          }
          }

          return $new_word;
          } catch (\Exception $ex) {
          return $word;
          } */
    }

    public function compareWord($word = '', $word_array = [], $edi_type = 'out', $accept_ignore = 0, $rule_id = 0) {
        $class_name = $edi_type == 'out' ? 'bg-warning' : 'bg-danger';
        $style = $edi_type == 'out' ? 'background-color:#ffc107!important; color:#343a40!important;' : 'background-color:#dc3545!important;color:#ffffff!important; text-weight:bold;';

        $ignore = false;
        if ($accept_ignore == 1 && in_array($rule_id, $this->accepted_rule_ids)) {
            $ignore = true;
        }

        try {
            $check_word = $this->removeSpecialCharacters(strtolower($word));
            $word2_array = [];

            if (!empty($word_array)) {
                foreach ($word_array as $word2) {
                    $word2_array[] = $this->removeSpecialCharacters(strtolower($word2));
                }
            }

            if ($check_word != "" && !in_array($check_word, $word2_array) && empty($ignore)) {
                return "<span class='" . $class_name . " text-dark' style='" . $style . "'>" . $word . "</span> ";
            }
        } catch (\Exception $ex) {
            return $word;
        }
    }

    public function diffComparing($type, $accept_ignore = 0, $data1, $data2) {
        //--------- define global variables -----------------------------------
        $default_compare_elements = $this->default_compare_elements;
        $free_text_rule_ids = $this->free_text_rule_ids;
        $accepted_rule_ids = $accept_ignore == 1 ? $this->accepted_rule_ids : [];
        //----------------------------------------------------------------------

        $return = $diff_rule_id = $all_rule_id = [];

        foreach ($data1 as $rule_id => $data) {
            $i = 0;
            $compare_elements_segments = isset($default_compare_elements[$rule_id]) ? $default_compare_elements[$rule_id] : [];
            $compare_elements = array_keys($compare_elements_segments);

            foreach ($data1[$rule_id] as $main_segment_key => $main_content) {
                $sigment_i = 0;
                if (is_array($main_content)) {
                    foreach ($main_content as $segment_key => $content) {
                        $sigment_i++;

                        $contents1 = $content;
                        $contents2 = isset($data2[$rule_id][$main_segment_key][$segment_key]) ? $data2[$rule_id][$main_segment_key][$segment_key] : [];

// $data_segemet = ''; //this is for sigment in single line

                        $compare_elements_segments_data = !empty($compare_elements_segments[$sigment_i]) ? $compare_elements_segments[$sigment_i] : [];
                        if (empty($compare_elements) || in_array($sigment_i, $compare_elements)) {

                            $count_rows = count($contents1) > count($contents2) ? count($contents1) : count($contents2);
//foreach ($content as $lnno => $val) //this is for sigment in single line
                            for ($lnno = 0; $count_rows > $lnno; $lnno++) {
                                $i++;
                                $segments_lnno = $lnno + 1;
                                $data_segemet = ''; //this is for sigment in next line

                                if (empty($compare_elements_segments_data[0]) || in_array($segments_lnno, $compare_elements_segments_data)) {
                                    $words = isset($contents1[$lnno]) ? trim($contents1[$lnno]) : '';
                                    $words2 = isset($contents2[$lnno]) ? trim($contents2[$lnno]) : '';
//print_r($words); echo '<br/>';
                                    $wordsArr1 = (explode(' ', $words));
                                    $wordsArr2 = isset($contents2[$lnno]) ? (explode(' ', $contents2[$lnno])) : [];
                                    $wordsArr2 = $this->array_change_value_case($wordsArr2, CASE_UPPER);

                                    if (in_array($rule_id, $free_text_rule_ids)) {
                                        $free_text_data1[$rule_id][] = $words;
                                        $free_text_data2[$rule_id][] = $words2;
                                    } else {

                                        if ($words == "" && $words2 == "") {
//skip line if both have white space
                                        } else {

                                            foreach ($wordsArr1 as $word) {
                                                $word = trim($word);
                                                $new_word = $this->compareWord($word, $wordsArr2, $type, $accept_ignore, $rule_id);

                                                if (trim($new_word) != "") {
                                                    $data_segemet .= $new_word;
                                                    $diff_rule_id[$rule_id] = $rule_id;
                                                } else {
                                                    if (empty($compare_elements) || in_array($sigment_i, $compare_elements)) {
                                                        $data_segemet .= $word . ' '; //remove unmatched segment
                                                    }
                                                }
//$all_rule_id[$rule_id] = $rule_id;
                                            }
                                        }
                                    }
                                }
                                $return[$rule_id][] = $data_segemet; //this is for sigment in next line
                            }
                        }
// $return[$rule_id][] = $data_segemet; // this is for sigment in single line
                    }
                }
                $all_rule_id[$rule_id] = $rule_id;
            }
        }

        if (!empty($free_text_data1)) {
            foreach ($free_text_data1 as $rule_id => $data) {
                $return[$rule_id][] = trim(implode(" ", $free_text_data1[$rule_id]));
            }
        }


        return [
            'data' => $return,
            'diff_rule_id' => $diff_rule_id,
            'all_rule_id' => $all_rule_id
        ];
    }

    public function freeTextDiff($type, $accept_ignore, $rule_id, $data1, $data2) {
        try {
            $data1_array = explode(" ", implode(" ", $data1));
            $data2_array = explode(" ", implode(" ", $data2));
            $data2_array = $this->array_change_value_case($data2_array, CASE_UPPER);
            $return_str = '';

            if (!empty($data1_array)) {
                foreach ($data1_array as $word) {
                    $new_word = $this->compareWord($word, $data2_array, $type, $accept_ignore, $rule_id);
                    if (trim($new_word) != "") {
                        $return_str .= $new_word;
                        $is_diff = true;
                    } else {
                        $return_str .= $word . ' ';
                    }
                }
            }

            return [
                'is_diff' => !empty($is_diff) ? true : false,
                'data_str' => $return_str
            ];
        } catch (\Exception $e) {
            return [
                'is_diff' => false,
                'data_str' => implode(" ", $data1)
            ];
        }
    }

    public function diffRows($id = 0, $carrier = '', $accept_ignore = 0, $out_edi_file = '', $in_edi_file = '') {
        $rule_obj = new Rule();
        $this->default_compare_elements = $rule_obj->defaultCompareElements($carrier);
        $this->accepted_rule_ids = (new Edi_meta_data())->acceptedRuleIds($id);

        $outgoingEdiData = new EdiReaderController($out_edi_file);
        $outgoing = [];

        $incomingEdidata = new EdiReaderController($in_edi_file);
        $incoming = [];

        $rulesArray = $rule_obj->getDefaultRules($carrier);

        $free_text_rule_ids = [];
        if (!empty($rulesArray)) {
            foreach ($rulesArray as $lineRules) {
                $id = $lineRules['id'];
                $filters = $lineRules['rules'];
                $outgoing[$id] = array_values($outgoingEdiData->readEdiDataValue($filters, $outgoingEdiData));
                $incoming[$id] = array_values($incomingEdidata->readEdiDataValue($filters, $incomingEdidata));

                if ($lineRules['is_free_text_compare'] == 1) {
                    $free_text_rule_ids[] = $id;
                }
            }
        }

        $this->free_text_rule_ids = $free_text_rule_ids;

        $outgoing_diff = $this->diffComparing('out', $accept_ignore, $outgoing, $incoming);
        $incoming_diff = $this->diffComparing('in', $accept_ignore, $incoming, $outgoing);

        $final_outgoing_diff = $final_incoming_diff = [];

//remove emapty is exist on both line and arrange
        $all_rule_id = array_unique(array_merge($outgoing_diff['all_rule_id'], $incoming_diff['all_rule_id']));

        foreach ($all_rule_id as $rule_id) {
            $outgoing_array = isset($outgoing_diff['data'][$rule_id]) ? $outgoing_diff['data'][$rule_id] : [];
            $incoming_array = isset($incoming_diff['data'][$rule_id]) ? $incoming_diff['data'][$rule_id] : [];

            $outgoing_count = count($outgoing_array);
            $incoming_count = count($incoming_array);

            $new_outgoing_diff = $new_incoming_diff = [];

            if (in_array($rule_id, $free_text_rule_ids)) {

                $free_out_diff = $this->freeTextDiff('out', $accept_ignore, $rule_id, $outgoing_array, $incoming_array);
                $new_outgoing_diff[] = isset($free_out_diff['data_str']) ? $free_out_diff['data_str'] : '';
                if (!empty($free_out_diff['is_diff'])) {
                    $outgoing_diff['diff_rule_id'][] = $rule_id;
                }

                $free_in_diff = $this->freeTextDiff('in', $accept_ignore, $rule_id, $incoming_array, $outgoing_array);
                $new_incoming_diff[] = isset($free_in_diff['data_str']) ? $free_in_diff['data_str'] : '';
                if (!empty($free_in_diff['is_diff'])) {
                    $incoming_diff['diff_rule_id'][] = $rule_id;
                }
            } else {

                $count = $outgoing_count > $incoming_count ? $outgoing_count : $incoming_count;

                for ($i = 0; $i < $count; $i++) {
                    $new_outgoing_str = isset($outgoing_diff['data'][$rule_id][$i]) ? $outgoing_diff['data'][$rule_id][$i] : '';
                    $new_incoming_str = isset($incoming_diff['data'][$rule_id][$i]) ? $incoming_diff['data'][$rule_id][$i] : '';

                    if ($new_outgoing_str == $new_incoming_str && $new_outgoing_str == '') {
                        
                    } else {

                        $new_outgoing_diff[] = $new_outgoing_str;
                        $new_incoming_diff[] = $new_incoming_str;
                    }
                }
            }

            $final_outgoing_diff['data'][$rule_id] = $new_outgoing_diff;
            $final_incoming_diff['data'][$rule_id] = $new_incoming_diff;
        }

        $diff_rule_id = array_values(array_unique(array_merge($outgoing_diff['diff_rule_id'], $incoming_diff['diff_rule_id'])));

        return [
            'outgoing' => $final_outgoing_diff,
            'incoming' => $final_incoming_diff,
            'diff_rule_id' => $diff_rule_id,
            'all_rule_id' => $all_rule_id
        ];
    }

    public function showfileAction(Request $request) {
        $type = $request->input('type');
        $id = $request->input('id');
        $data = Edi_data::find($id);
        $gen_hash = md5($id);

        $compare_to_data = Edi_data::find($data->compared_with);
        $metas = Edi_meta_data::where('edi_data_id', $data->id)->get()->collect()->toArray();

        $rulesArray = (new Rule())->getDefaultRules($data->carrier);
        $diff_rows = $this->diffRows($id, $data->carrier, $accept_ignore = 0, $data->data, $compare_to_data->data);

        $country_name = '';
        $country_documents = (new CountryDocument())->getCountryDocumentsByName($country_name);

        $edi_status = (new EdiTitle())->getStatusList();
        
        return view('showfileaction')
//->with('contents', $contents)
                        ->with('type', $type)
                        ->with('country_documents', [
                            'country_name' => $country_name,
                            'documents' => $country_documents,
                        ])
                        ->with('edi', $data)
                        ->with('compare_to_data', $compare_to_data)
                        ->with('not_in_in', isset($diff_rows['outgoing']['diff_rule_id']) ? $diff_rows['outgoing']['diff_rule_id'] : [])
                        ->with('not_in_out', isset($diff_rows['incoming']['diff_rule_id']) ? $diff_rows['incoming']['diff_rule_id'] : [])
                        ->with('diff_rows', $diff_rows)
                        ->with('metas', $metas)
                        ->with('rules', $rulesArray)
                        ->with('edi_status', $edi_status)
                        ->with('hash', $gen_hash);
;
    }

    public function getEditDetailsWithDiff($id) {
        $data = Edi_data::find($id);
        $metas = Edi_meta_data::where('edi_data_id', $id)->get()->collect()->toArray();

        $rulesArray = (new Rule())->getDefaultRules($data->carrier);
        $rulesFilters = [];
        foreach ($rulesArray as $lineRules) {
            $rulesFilters[$lineRules['id']] = array_filter(explode('+', $lineRules['rules']));
        }
        $outgoingEdi = new Reader(storage_path('app') . $data->data);
        $outgoingEdiData = $outgoingEdi->getParsedFile();
//print_r($outgoingEdiData);
        $outgoingdataFormated = [];
        foreach ($rulesFilters as $id => $filters) {
            $outgoingdataFormated[$id] = $outgoingEdi->dataExtractor2($filters, $outgoingEdiData);
        }
        $compare_to_data = Edi_data::find($data->compared_with);
        $incomingEdi = new Reader(storage_path('app') . $compare_to_data->data);
        $incomingEdidata = $incomingEdi->getParsedFile();
        $incomingdataFormated = [];
        foreach ($rulesFilters as $id => $filters) {
            $incomingdataFormated[$id] = $incomingEdi->dataExtractor2($filters, $incomingEdidata);
        }
        return [
            'edi' => $data,
            'owner_email' => $data->owner_email,
            'compare_to_data' => $compare_to_data,
            'not_in_in' => collect(json_decode($data->not_in_in)),
            'not_in_out' => collect(json_decode($data->not_in_out)),
            'outgoing' => $outgoingdataFormated,
            'incoming' => $incomingdataFormated,
            'rules' => $rulesArray,
            'metas' => $metas
        ];
    }

    private function array_flatten_words($array = null) {
        $result = array();

        if (!is_array($array)) {
            $array = func_get_args();
        }

        foreach ($array as $key => $value):
            if (str_word_count($value, 0, '@+:.0..9') > 1) {
                $words = str_word_count($value, 1, '@+.:0...9');
                foreach ($words as $key => $word) {
                    if (is_numeric($word)) {
                        if (in_array($word, range(1, 9, 1))) {
                            
                        } else {
                            $words[$key] = (float) $word;
                        }
                    }
                }
                $result = array_merge($result, $words);
            } else {
                if (is_numeric($value)) {
                    if (in_array($value, range(1, 9, 1))) {
                        
                    } else {
                        $result = array_merge($result, array($key => (float) $value));
                    }
                } else {
                    $result = array_merge($result, array($key => trim($value)));
                }
            }

        endforeach;
        return $result;
    }

    private function findDiffinData($rule_id, $outgoing, $incoming) {
        $diff = [
            'notinincomming' => [],
            'notinoutgoing' => [],
        ];

        $diff['notinincomming'] = array_diff($this->array_flatten_words(collect($outgoing)->flatten()->toArray()), $this->array_flatten_words(collect($incoming)->flatten()->toArray()));
        $diff['notinincomming'] = array_map('trim', $diff['notinincomming']);


        $diff['notinoutgoing'] = array_diff($this->array_flatten_words(collect($incoming)->flatten()->toArray()), $this->array_flatten_words(collect($outgoing)->flatten()->toArray()));
        $diff['notinoutgoing'] = array_map('trim', $diff['notinoutgoing']);

        return $diff;
    }

    public function storeMessage(Request $request, $type = "out") {
        $store_target = $type === 'out' ? '/meridian/' : '/edi/';
        $message['edi'] = $request->input('edimessage');
        $filename = $request->input('edifilename') ?? 'edi_' . str_replace([' ', ':'], ['_', '_'], now()) . '_filefrommeridian.edi';
        if ($request->input('edimessage')) {
            Storage::disk('local')->put($store_target . $filename, $message['edi']);
        }
        $message['status'] = 200;
        $message['message'] = 'Edi file Saved in disk as ' . $filename;

        $email_data['user'] = $request->user();
        $email_data['to_emails'] = config('mail.mail_edi_store_file');
        $email_data['date'] = date('Y-m-d H:i:s', strtotime(now()));
        $email_data['file_name'] = $filename;
        $email_data['subject'] = "Outgoing EDI file saved in meridian from API";
        $email_data['message'] = $message['message'];
//(new EdiEmailController())->sendEmailStoreEDIFile($email_data);
        return json_encode($message);
    }

    public function storeEdiFilebase64(Request $request, $type = "out") {
        try {

            $message['status'] = 200;

            $store_target = $type === 'out' ? '/meridian/' : '/edi/';
            $edimessage = $request->input('edimessage');
            if (empty(trim($edimessage))) {
                $message['message'] = 'edimessage input is required.';
                return $message;
            }

            $edimessage = trim(base64_decode($edimessage));
            $filename = 'edi_' . str_replace([' ', ':'], ['_', '_'], now()) . '_file_from_meridian.edi';
            $save = Storage::disk('local')->put($store_target . $filename, $edimessage);

            if (empty($save)) {
                return $message['message'] = 'Edi file Not Saved in disk.';
            }

            $message['edi'] = $edimessage;
            $message['status'] = 200;
            $message['message'] = 'Edi file Saved in disk as ' . $filename;

            $email_data['user'] = $request->user();
            $email_data['to_emails'] = config('mail.mail_edi_store_file');
            $email_data['date'] = date('Y-m-d H:i:s', strtotime(now()));
            $email_data['file_name'] = $filename;
            $email_data['subject'] = "Outgoing EDI file saved in meridian from API";
            $email_data['message'] = $message['message'];
            (new EdiEmailController())->sendEmailStoreEDIFile($email_data);

            return $message;
        } catch (\Exception $e) {
            $message['status'] = 200;
            return $message['message'] = 'ERROR: Edi file Not Saved in disk.';
        }
    }

    public function storeMessageFile(Request $request) {
        $store_target = '/meridian/';
        $ediFile = $request->edimessagefile;
        $message['status'] = 201;
        $message['message'] = 'Failed to save content';
        if ($ediFile !== " ") {
            $message['edi'] = $ediFile;  //->input('edimessage');
            $filename = 'edi_' . str_replace([' ', ':'], ['_', '_'], now()) . '_filefrommeridian.edi';
            Storage::disk('local')->put($store_target . $filename, $message['edi']);
            $message['status'] = 200;
            $message['message'] = 'Edi file Saved in disk as ' . $filename;

            $email_data['user'] = $request->user();
            $email_data['to_emails'] = config('mail.mail_edi_store_file');
            $email_data['date'] = date('Y-m-d H:i:s', strtotime(now()));
            $email_data['file_name'] = $filename;
            $email_data['subject'] = "Outgoing EDI file saved in meridian from API";
            $email_data['message'] = $message['message'];
            (new EdiEmailController())->sendEmailStoreEDIFile($email_data);
        }
        return json_encode($message);
    }

    public function processOutgoingEdi(Request $request) {
        $Edi_data = new Edi_data;
        $files = Storage::allFiles('meridian');
        foreach ($files as $file) {
            $path = Storage::path($file);
            $reader = new Reader($path);
            $FileContent = $reader->getParsedFile();
            $sender = $reader->readUNBInterchangeSender();
            $receipent = $reader->readUNBInterchangeRecipient();

            $bn = $reader->readEdiDataValue(['RFF', ['1.0' => 'BN']], 1, 1);
            $ff = $reader->readEdiDataValue(['RFF', ['1.0' => 'FF']], 1, 1);
            if (!empty($reader->dataExtractor(['NAD', 'CA']))) {
                $carrier = strtoupper(collect($reader->dataExtractor(['NAD', 'CA']))->flatten()[0]);
            } else {
                $carrier = 'Carrier Missing';
            }
            $dtm = $reader->readUNBDateTimeOfPreperation();
            if ($request->validate([$dtm => 'date_format:Y/m/d h:i:s'])) {
                
            } else {
                $dtm = null;
            };
            $in_or_out = 0;
            $dttm = $reader->readEdiSegmentDTM('137');
            $comm = $reader->dataExtractor(array('COM'));
            $com = 'Owner missing';
            foreach ($comm as $comdata) {
                if (strtolower($comdata[1]) === 'em' && strtolower($comdata[0]) !== 'csnotification@bdpint.com') {
                    $com = strtolower($comdata[0]);
                }
            }
        }
    }

    private function setDiskforCarrier($scac, $carrier_id = 0) {
        try {
            $disks = config('filesystems.disks.' . $scac);
            if (!$disks) {
                if ($carrier_id > 0) {
                    $carrier = CarrierSetup::where('carrier_scac', $scac)
                            ->where('id', $carrier_id)
                            ->first();
                } else {
                    $carrier = CarrierSetup::where('carrier_scac', $scac)
                            ->where('is_ftp', 1)
                            ->where('ftp_location', '!=', '')
                            ->where('ftp_userid', '!=', '')
                            ->where('ftp_password', '!=', '')
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->first();
                }

                if ($carrier) {
                    Config::set('filesystems.disks.' . $scac, ['driver' => 'ftp', 'host' => $carrier->ftp_location, 'username' => $carrier->ftp_userid, 'password' => $carrier->ftp_password]);

                    return true;
                }
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function processIncomingEdi(Request $request, $scac, $carrier_id = 0) {
        $environment = App::environment();
        $return_data = '';

        try {
            if ($carrier_id > 0) {
                $rows = CarrierSetup::select('id', 'carrier_scac', 'folder_type', 'folder_location')
                        ->where('id', $carrier_id)
                        ->where('status', 1)
                        ->where('is_ftp', 1)
                        ->whereNull('deleted_at')
                        ->get();
            } else {
                $rows = CarrierSetup::select('id', 'carrier_scac', 'folder_type', 'folder_location')
                        ->where('carrier_scac', $scac)
                        ->where('status', 1)
                        ->where('is_ftp', 1)
                        ->whereNull('deleted_at')
                        ->get();
            }

            if (!empty($rows)) {
                foreach ($rows as $row) {

                    $carrier_id = $row->id;
                    $scac = $row->carrier_scac;

                    if (empty($this->setDiskforCarrier($scac, $carrier_id))) {
                        $return_data .= $scac . ": Carrier FTP details not not found. Can't able to download files.<br/>";
                    }

                    $folder_type = !empty($row->folder_type) ? $row->folder_type : 'IN';
                    $folder_location = !empty($row->folder_location) ? $row->folder_location : $scac;

                    $ftp_folder = $folder_location . '/' . $folder_type;
                    $ftp_archive_folder = $folder_location . '/ARCHIVE' . '/' . $folder_type;
                    $local_folder_name = ((strtoupper($folder_type) == 'OUT') ? 'meridian' : 'edi' ) . '/' . $scac;

                    $files = Storage::disk($scac)->allFiles($ftp_folder);

                    if (!empty($files) && !empty($folder_type)) {

                        $Edi_data = new Edi_data;
                        $ftp = Storage :: disk($scac);

                        if ($environment === 'production') {
                            $ftp->makeDirectory($ftp_archive_folder);
                        }

                        foreach ($files as $file) {
                            try {
                                $file_name = basename($file);
                                $return_data .= 'downloading content from ' . $file . '<br/>';
                                $content = Storage::disk($scac)->get($file);
                                $return_data .= 'writing content to local <br/>';
                                $result = Storage::put($local_folder_name . '/' . $file_name, $content);
                                $return_data .= 'writing finised on local <br/>';
                                if (!empty($result) && $environment === 'production') {
                                    $ftp->move($file, $ftp_archive_folder . '/' . $file_name);
                                    $return_data .= $file . ' file move in ARCHIVE folder of FTP <br/>';
                                }
                            } catch (\Exception $e) {
                                $return_data .= "ERROR: " . $e->getMessage();
                            }
                        }
                    } else if (empty($folder_type)) {
                        $return_data .= '<br/>' . $scac . ": FTP details not present in DB.<br/>";
                    } else {
                        $return_data .= '<br/>' . $scac . ": Edi files are not found on server.<br/>";
                    }
                }
            }
        } catch (\Exception $e) {
            $return_data .= "<br/>ERROR: " . $e->getMessage();
        }

        if ($request->formate == 'prity') {
            echo!empty($return_data) ? $return_data : "";
        } else {
            return [
                'success' => true,
                'message' => !empty($return_data) ? $return_data : '',
            ];
        }
    }

    public function diffDownload(Request $request) {
        $downloadFile = (isset($request->saveAttachment) && $request->saveAttachment == true) ? 0 : 1;
        $id = $request->input('id');
        $AllReqData = $this->getEditDetailsWithDiff($id);

        $metaFormated = [];
        $metaFormated_carrier_reject = [];
        $metaFormated_bdp_reject = [];
        $AllReqData['downloadtype'] = $request->downloadtype; // pdf or csv
        $AllReqData['intent'] = $request->allordiffonly;  // all or diffonly
        $AllReqData['feedback'] = $request->withcomments; // with // without
        $AllReqData['email_type'] = $request->email_type; // with // without
        //$diff_rows = $this->diffRows($id, $data->carrier, $accept_ignore = 0, $data->data, $compare_to_data->data);

        $diff_rows = $this->diffRows($id, $AllReqData['edi']->carrier, $accept_ignore = 1, $AllReqData['edi']->data, $AllReqData['compare_to_data']->data);

        $ruleKeysDiffonly = $diff_rows['diff_rule_id'];

        $AllReqData['ruleKeysDiffonly'] = $ruleKeysDiffonly;

        if ($AllReqData['intent'] == 'diffonly') {
            $newRules = [];
            foreach ($AllReqData['rules'] as $rules) {
                if (in_array($rules['id'], $ruleKeysDiffonly, true) && !empty($ruleKeysDiffonly)) {
                    $newRules[] = $rules;
                }
            }
            $AllReqData['rules'] = $newRules;
        }

        $metas_rows = $AllReqData['metas'];
        $metas = [];

        if (!empty($metas_rows) && !empty($diff_rows['diff_rule_id'])) {
            foreach ($metas_rows as $meta) {
                if (in_array($meta['rules_id'], $diff_rows['diff_rule_id'])) {
                    $metas[$meta['id']] = [
                        'rules_id' => $meta['rules_id'],
                        'reason_code' => $meta['reason_code'],
                        'is_accepted' => $meta['is_accepted'],
                        'is_carrier_reject' => $meta['is_carrier_reject'],
                        'is_bdp_reject' => $meta['is_bdp_reject'],
                        'carrier_reject_msg' => $meta['carrier_reject_msg'],
                        'bdp_reject_msg' => $meta['bdp_reject_msg'],
                    ];
                }
            }
        }
        $AllReqData['metas'] = $metas;

        $fileName = $AllReqData['edi']['carrier'] . "_";
        $fileName .= (!empty($AllReqData['edi']['ff_no'])) ? "FF_" . $AllReqData['edi']['ff_no'] . "_" : "FF_TBD_";
        $fileName .= (!empty($AllReqData['edi']['booking_no'])) ? "BN_" . $AllReqData['edi']['booking_no'] . "_" : "BN_TBD_";
        $fileName .= date('Ymd_His', strtotime("now"));

        $fileName = str_replace([' ', '.'], ['_'], $fileName);

        if ($AllReqData['downloadtype'] == 'pdf') {
            $fileName = $fileName . '.pdf';

            ob_start();
            $html = view('pdf')->with(compact('AllReqData'))->with(compact('diff_rows'))->render();
            $html = str_replace(' & ', '&amp;', $html);

            $dompdf = new Dompdf();
            $dompdf->loadHtml(html_entity_decode($html));

// (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4');
            if ($AllReqData['feedback'] == 'with') // set to landscape for feedback 
                $dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
            $content = $dompdf->render();
            Storage::put('/public/download/' . $fileName, $dompdf->output(), 'public');

            ob_end_clean();

// Output the generated PDF to Browser
            if ($downloadFile) {
                $dompdf->stream($fileName);
            }
        }
        if ($AllReqData['downloadtype'] == 'csv') {
            $fileName = $fileName . '.xlsx';
            ob_start();

            $html = view('pdf')->with(compact('AllReqData'))->with(compact('diff_rows'))->render();
            $html = str_replace(' & ', '&amp;', $html);
            $html = str_replace('>&<', '>&amp;<', $html);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $spreadsheet = $reader->loadFromString($html);
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(storage_path('/app/public/download/') . $fileName);

            if ($downloadFile) {
                return Storage::download('/public/download/' . $fileName);
            }
        }

        if ($downloadFile == 0) {
            return $fileName;
        }
    }

    public function saveDocumentStatus(Request $request) {
        $id = $request->id;
        $EdiData = Edi_data::find($id);
        $EdiData->is_locked = 1;
        $EdiData->save();
        $message['data'] = true;
        return response()->json($message);
    }

    public function saveEmailAttachment(Request $request) {

        $request->saveAttachment = true;
        $fileName = $this->diffDownload($request);

        return response()->json([
                    'file_path' => storage_path('app/public/download/') . $fileName,
                    'file_name' => $fileName,
                    'mime_type' => ($request->downloadtype == 'pdf') ? 'application/pdf' : 'text/csv'
        ]);
    }

    public function download(Request $request) {
        return Storage::download($request->path);
    }

}
