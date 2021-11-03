<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Edi_data;
use App\Models\EdiTitle;
use App\Models\UserCarrier;
use App\Models\CarrierSetup;
use Carbon\Carbon;
use App\Models\Country;


class DashboardController extends Controller {

    public function show() {
        $carrier_data = CarrierSetup::select([
            'id',
            'carrier_scac',
            'status'
        ])->where('status', 1)->orderBy('carrier_scac', 'ASC')->get();
        $edi_status = (new EdiTitle())->getStatusList();

        $country_rows = Country::select([
            'id',
            'country_code',
            'country_name'
        ])->where('status', 1)->where('country_code', '!=', '')->orderBy('country_code', 'ASC')->get();
        
        return view('dashboard')->with('carrier_data', $carrier_data)->with('edi_status', $edi_status)->with('all_countries', $country_rows);
    }


    public function ajax(Request $request){

        if ($request->ajax()) {

            $user = auth()->user();

            $totalFilteredRecord = $totalDataRecord = $draw_val = "";
            $limit_val = $request->input('length');
            $start_val = $request->input('start');

   
            $request_data = $request->input();
            $order   = $request->input('order') ? $request->input('order') : [];
            
            if(isset($order[0]['column'])){

                switch($order[0]['column']){
                    case 0:
                        $column = 'dtm';
                        break;
                    case 1:
                        $column = 'received_date';
                        break;
                    case 2:
                        $column = 'diff_seconds_abs'; //diff_second_abs //'days';
                        break;
                    case 3:
                        $column = 'owner_email';
                        break;
                    case 4:
                        $column = 'country_code';
                        break;
                    case 5:
                        $column = 'booking_no';
                        break;
                    case 6:
                        $column = 'ff_no';
                        break;
                    case 7:
                        $column = 'bn_no';
                        break;
                    case 8:
                        $column = 'carrier';
                        break;
                    case 9:
                        $column = 'title';
                        break;
                    case 10:
                        $column = 'compared_with'; //'sent_edi';
                        break;
                    case 11:
                        $column = 'compared_with';
                        break;
                    case 12:
                        $column = 'compared_with'; 
                        break;
                    default:
                        $column = 'dtm';
                        break;

                }

                $order_val = $column;
                $dir_val = $order[0]['dir']; 
            }
            else{
                $order_val = 'dtm';
                $dir_val = 'DESC'; 
            }
           

            $user = auth()->user();
            
            // Total number of records without filtering
            $data = Edi_data::select('edi_datas.*', \DB::raw('ABS(diff_seconds) as diff_seconds_abs'),'edi_titles.title')
                    ->join('edi_titles','edi_datas.status','=','edi_titles.status_code');

            $carrier_scac = [];
            
            $active_carrier_scac = CarrierSetup::select('carrier_scac')
                ->where('status', 1)
                ->get()
                ->pluck('carrier_scac')
                ->toArray();

            if ($user->is_admin != 1 && $user->is_all_carriers != 1) {

                $carriers = UserCarrier::where('user_id', $user->id)->with('getCarriers')->get()->collect()->toArray();
    
                if (!empty($carriers)) {
                    foreach ($carriers as $carrier) {
                        if (!empty($carrier['get_carriers'])) {
                            foreach ($carrier['get_carriers'] as $a) {
                                $carrier_scac[] = $a['carrier_scac'];
                            }
                        }
                    }
                }
                $data = $data->whereIN('carrier', $carrier_scac);
            }
   
            $no_of_days_for_data_load = config('app.no_of_days_for_data_load');
            // Search 
            if(isset($request_data[0]['value']) && $request_data[0]['value'] == "search_all"){
                $search_arr = array();
                for($i = 0; $i <= 9; $i++){
                    $search_arr[$request_data[$i]['name']] = $request_data[$i]['value'];
                }
                if($search_arr['dtm'] == "" && $search_arr['received_date'] == "" && $search_arr['owner_name'] == "" && $search_arr['country_code'] == "" && $search_arr['booking_no'] == "" && $search_arr['ff_no'] == "" && $search_arr['bn_no'] == "" && $search_arr['carrier'] == "" && $search_arr['status'] == "")
                {
                    $dtm_start_date_time = date('Y-m-d H:i:s', strtotime(' - '.$no_of_days_for_data_load.' days'));
                    $dtm_end_date_time = date('Y-m-d H:i:s');
                    $data = $data->whereBetween('dtm', [$dtm_start_date_time,$dtm_end_date_time]);
                }
                if(isset($search_arr['dtm']) && !empty($search_arr['dtm']) && isset($search_arr['received_date']) && !empty($search_arr['received_date']))
                {
                    $start_date_sel         = date('Y-m-d H:i:s', strtotime($search_arr['dtm']));
                    $received_date_format   = date('Y-m-d', strtotime($search_arr['received_date']));
                    $end_date_sel           = $received_date_format . " 23:59:59";
                   
                    $data = $data->where(function($query) use ($start_date_sel, $end_date_sel){
                        $query->whereBetween('dtm', [$start_date_sel, $end_date_sel])
                              ->orWhereBetween('received_date', [$start_date_sel, $end_date_sel]);
                    });
                }
                else
                {
                    if(isset($search_arr['dtm']) && !empty($search_arr['dtm']))
                    {
                        $date_format = date('Y-m-d', strtotime($search_arr['dtm']));
                        $date = date('Y-m-d H:i:s', strtotime($search_arr['dtm']));
                        $date_end = $date_format . " 23:59:59";
                        $data = $data->whereBetween('dtm', [$date,$date_end]);
                    }
                    if(isset($search_arr['received_date']) && !empty($search_arr['received_date']))
                    {
                        $received_date_format = date('Y-m-d', strtotime($search_arr['received_date']));
                        $received_date = date('Y-m-d H:i:s', strtotime($search_arr['received_date']));
                        $received_date_end = $received_date_format . " 23:59:59";
                        $data = $data->whereBetween('received_date', [$received_date,$received_date_end]);
                    }
                }
                if(isset($search_arr['owner_name']) && !empty($search_arr['owner_name']))
                    $data = $data->where('owner_name', 'LIKE', "%{$search_arr['owner_name']}%");

                if(isset($search_arr['country_code']) && !empty($search_arr['country_code']))
                    $data = $data->where('country_code', 'LIKE', "%{$search_arr['country_code']}%");

                if(isset($search_arr['booking_no']) && !empty($search_arr['booking_no']))
                    $data = $data->where('booking_no', 'LIKE', "%{$search_arr['booking_no']}%");

                if(isset($search_arr['ff_no']) && !empty($search_arr['ff_no']))
                    $data = $data->where('ff_no', 'LIKE', "%{$search_arr['ff_no']}%");

                if(isset($search_arr['bn_no']) && !empty($search_arr['bn_no']))
                    $data = $data->where('bn_no', 'LIKE', "%{$search_arr['bn_no']}%");

                if(isset($search_arr['carrier']) && !empty($search_arr['carrier']))
                    $data = $data->where('carrier', $search_arr['carrier']);

                if(isset($search_arr['status']) && $search_arr['status'] != "")
                    $data = $data->where('edi_datas.status', $search_arr['status']);

                $data = $data->where('in_or_out', 0)->whereIn('carrier', $active_carrier_scac);
                
                if(!empty($carrier_scac)){
                    $data = $data->whereIN('carrier', $carrier_scac);
                }

               
                $count_filter = $data->count();
                $count_total = $count_filter;
                
            } else{
                $dtm_start_date_time = date('Y-m-d H:i:s', strtotime(' - '.$no_of_days_for_data_load.' days'));
                $dtm_end_date_time = date('Y-m-d H:i:s');
                $data = $data->whereBetween('dtm', [$dtm_start_date_time,$dtm_end_date_time]);
                $data = $data->where('in_or_out', 0)->whereIn('carrier', $active_carrier_scac);

                if(!empty($carrier_scac)){
                    $data = $data->whereIN('carrier', $carrier_scac);
                }

                $count_total = $data->count();
                $count_filter = $count_total;
            }

            //\DB::enableQueryLog();
           
            $data = $data->offset($start_val)
                    ->limit($limit_val)
                    ->orderBy($order_val,$dir_val)
                    ->groupBy('edi_datas.id')
                    ->get();

            //$query = \DB::getQueryLog();
            //dd($query);

            //Prepare records
            $result = [];
            $edi_status = (new EdiTitle())->getStatusList();

           

            foreach($data as $record) {
                
                if (!empty($record['dtm']) && !empty($record['received_date'])) {
                    $start_date = $record['dtm'];
                    $end_date = $record['received_date'];
                    $datetime1 = new \DateTime($start_date);
                    $datetime2 = new \DateTime($end_date);
                    $interval = $datetime1->diff($datetime2);
                    $h=intval($interval->format("%a"))*24+intval($interval->format("%H"));
                    $days = $h;
                    $days .= ':';
                    $days .= $interval->format("%I");

                }else{
                    $days='0:00';
                }

                $v = explode('@', $record['owner_email']);
                $record['owner_email'] = ucwords(str_replace('.', ' ', $v[0]));

                $dtm = !empty($record['dtm']) ? date("j M Y",strtotime($record['dtm'])) : '';
                if($dtm !=''){
                    $dtm = str_replace(" ","&nbsp;",$dtm);
                    $dtm.= '<br> ';
                    $dtm.= date("H:i",strtotime($record['dtm']));
                }

                $received_date = !empty($record['received_date']) ? date("j M Y",strtotime($record['received_date'])) : '';
                if(!empty($record['received_date'])){
                    $received_date = str_replace(" ","&nbsp;",$received_date);
                    $received_date .= '<br> ';
                    $received_date .= date("H:i",strtotime($record['received_date']));
                }

                $js_code = "submit_form(this)";
                $record_file_name = explode('/', $record['data']);
                $sent_edi_file_name = "";
                if(isset($record_file_name) && count($record_file_name) > 0)
                    $sent_edi_file_name = $record_file_name[count($record_file_name) - 1];

                $rec_edi_file_name = "";
                $receive_edi_data = Edi_data::where('id', $record['compared_with'])->get()->collect()->toArray();
                if(isset($receive_edi_data[0]['data']) && !empty($receive_edi_data[0]['data']))
                {
                    $record_rec_file_name = explode('/', $receive_edi_data[0]['data']);
                    if(isset($record_rec_file_name) && count($record_rec_file_name) > 0)
                        $rec_edi_file_name = $record_rec_file_name[count($record_rec_file_name) - 1];
                }
                    
                $result[] = array( 
                    "dtm"=> $dtm,
                    "received_date"=> $received_date,
                    "days"=> $days,
                    "owner_email"=> $record['owner_email'],
                    "country_code"=> $record['country_code'],
                    "booking_no"=> $record['booking_no'],
                    "ff_no" => str_replace(['BDP', 'bdp'], '', $record['ff_no']),
                    "bn_no" => $record['bn_no'],
                    "carrier" => $record['carrier'],
                    "status" => !empty($edi_status[$record['status']]) ? $edi_status[$record['status']] : 'Not found',
                    'sent_edi' =>  "<form method='POST' action='".route('show.file')."' target='_blank'>
                                        <input type='hidden' name='_token' value='".csrf_token()."' />
                                        <input type='hidden' value='".$record['id']."' name='id' />
                                        <input type='hidden' value='outgoing' name='type' />
                                        <a href='".route('show.file')."' onclick='event.preventDefault();".$js_code."'>
                                            <i class='material-icons' style='vertical-align:middle;' title='".$sent_edi_file_name."'>file_present</i>
                                        </a>
                                    </form>",
                    'received_edi' => (!empty($record['compared_with'])) ? "<form method='POST' action='".route('show.file')."' target='_blank'>
                                        <input type='hidden' name='_token' value='".csrf_token()."' />
                                        <input type='hidden' value='".$record['compared_with']."' name='id' />
                                        <input type='hidden' value='incoming' name='type' />
                                        <a href='".route('show.file')."' onclick='event.preventDefault();".$js_code."'>
                                            <i class='material-icons' style='vertical-align:middle;' title='".$rec_edi_file_name.
                                            "'>file_present</i>
                                        </a>
                                    </form>" : "",
                    'diff' => (!empty($record['compared_with'])) ? "<form method='POST' action='".route('show.file.action')."' target='_blank'>
                                    <input type='hidden' name='_token' value='".csrf_token()."' />
                                    <input type='hidden' value='".$record['id']."' name='id' />
                                    <input type='hidden' value='diff' name='type' />
                                    <a href='show-file-action?id=".$record['id']."&type=diff' target='_blank'>
                                        <span class='action_icons'><img src='".asset('img/link_black_24dp.svg')."'></span>
                                    </a>
                                </form>" : "",
                                //"action" => "",

                );
            }

            
            $draw_val = $request->input('draw');
            $get_json_data = array(
                "draw"            => intval($draw_val),
                "recordsTotal"    => intval($count_total),
                "recordsFiltered" => intval($count_filter),
                "data"            => $result
            );
 
            echo json_encode($get_json_data);
        }
    }

}