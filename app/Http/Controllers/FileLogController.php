<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCarrier;
use App\Models\CarrierSetup;
use App\Models\Edi_data;
use App\Models\EdiTitle;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class FileLogController extends Controller {

    public function index(Request $request) {
            $user = auth()->user();
            if ($user->is_admin != 1 && $user->is_all_carriers != 1) {
                
                $carriers = UserCarrier::select('carrier_setups.*')
                ->where('user_carriers.user_id', $user->id)
                ->join('carrier_setups','user_carriers.carrier_id','=','carrier_setups.id')
                ->get()->collect()->toArray();

            }else{
                $carriers = CarrierSetup::select('*')
                ->where('status', 1)
                ->get()
                ->toArray();
            }

            $rows = [];
            foreach($carriers as $carrier){
                $scac = $carrier['carrier_scac'];
                $data = Edi_data::where('carrier','=',$scac)->where('in_or_out',1);
                $count = $data->count();
                $count_24hrs = $data->where('created_at', '>', Carbon::now()->subDays(1))->count();
                $rows[] = [
                    "carrier" => $carrier['carrier_name'],
                    "count" => $count,
                    "count_in_24hrs" => $count_24hrs,
                    "scac" => $scac,
                ];
            }
        return view('filelog/list')->with('rows', $rows);
    }

    public function showCarrier($scac) {
        $carrier_data = CarrierSetup::select([
            'id',
            'carrier_scac',
            'status'
        ])->where('status', 1)->where('carrier_scac',$scac)->orderBy('carrier_scac', 'ASC')->get();
        $edi_status = (new EdiTitle())->getStatusList();
        return view('filelog/carrierlist')->with('carrier_data', $carrier_data)->with('edi_status', $edi_status)->with('carrier_scac',$scac);
    }

    public function ajax(Request $request){

        if ($request->ajax()) {

            $totalFilteredRecord = $totalDataRecord = $draw_val = "";
            $limit_val = $request->input('length');
            $start_val = $request->input('start');

   
            $request_data = $request->input();
            $order   = $request->input('order') ? $request->input('order') : [];
            
            if(isset($order[0]['column'])){

                switch($order[0]['column']){
                    case 0:
                        $column = 'created_at';
                        break;
                    case 1:
                        $column = 'booking_no';
                        break;
                    case 2:
                        $column = 'ff_no';
                        break;
                    case 3:
                        $column = 'bn_no';
                        break;
                    case 4:
                        $column = 'carrier';
                        break;
                    case 5:
                        $column = 'compared_with'; //'sent_edi';
                        break;
                    case 6:
                        $column = 'compared_with';
                        break;
                    case 7:
                        $column = 'compared_with'; 
                        break;
                    default:
                        $column = 'received_date';
                        break;

                }

                $order_val = $column;
                $dir_val = $order[0]['dir']; 
            }
            else{
                $order_val = 'received_date';
                $dir_val = 'DESC'; 
            }
           

            $user = auth()->user();
            
            // Total number of records without filtering
            $data = Edi_data::select('edi_datas.*', \DB::raw('ABS(diff_seconds) as diff_seconds_abs'),'edi_titles.title')
                    ->join('edi_titles','edi_datas.status','=','edi_titles.status_code');

           
   
            // Search 
            if(isset($request_data[0]['value']) && $request_data[0]['value'] == "search_all"){
                $search_arr = array();
                for($i = 0; $i <= 7; $i++){
                    $search_arr[$request_data[$i]['name']] = $request_data[$i]['value'];
                }
                /*if($search_arr['dtm'] == "" && $search_arr['received_date'] == "" && $search_arr['owner_name'] == "" && $search_arr['booking_no'] == "" && $search_arr['ff_no'] == "" && $search_arr['bn_no'] == "" && $search_arr['carrier'] == "" && $search_arr['status'] == "")
                {
                    $dtm_start_date_time = date('Y-m-d H:i:s', strtotime(' - 1 days'));
                    $dtm_end_date_time = date('Y-m-d H:i:s');
                    $data = $data->whereBetween('dtm', [$dtm_start_date_time,$dtm_end_date_time]);
                }
                if(isset($search_arr['dtm']) && !empty($search_arr['dtm']))
                {
                    $date_format = date('Y-m-d', strtotime($search_arr['dtm']));
                    $date = date('Y-m-d H:i:s', strtotime($search_arr['dtm']));
                    $date_end = $date_format . " 23:59:59";
                    $data = $data->whereBetween('dtm', [$date,$date_end]);
                }*/
                if(isset($search_arr['received_date']) && !empty($search_arr['received_date']))
                {
                    $received_date_format = date('Y-m-d', strtotime($search_arr['received_date']));
                    $received_date = date('Y-m-d H:i:s', strtotime($search_arr['received_date']));
                    $received_date_end = $received_date_format . " 23:59:59";
                    $data = $data->whereBetween('edi_datas.created_at', [$received_date,$received_date_end]);
                }
                /*if(isset($search_arr['owner_name']) && !empty($search_arr['owner_name']))
                    $data = $data->where('owner_name', 'LIKE', "%{$search_arr['owner_name']}%");*/

                if(isset($search_arr['booking_no']) && !empty($search_arr['booking_no']))
                    $data = $data->where('booking_no', 'LIKE', "%{$search_arr['booking_no']}%");

                if(isset($search_arr['ff_no']) && !empty($search_arr['ff_no']))
                    $data = $data->where('ff_no', 'LIKE', "%{$search_arr['ff_no']}%");

                if(isset($search_arr['bn_no']) && !empty($search_arr['bn_no']))
                    $data = $data->where('bn_no', 'LIKE', "%{$search_arr['bn_no']}%");

                if(isset($search_arr['carrier']) && !empty($search_arr['carrier']))
                    $data = $data->where('carrier', $search_arr['carrier']);

                if(isset($search_arr['status']) && $search_arr['status'] != ""){

                    if($search_arr['status']=="success"){
                        $data = $data->where('compared_with',">",0);
                    }else if($search_arr['status']=="pending"){
                        $data = $data->where('compared_with',"=",0);
                    }else{
                        $data = $data->where('compared_with',"<",0);
                    }
                }
                    

                $data = $data->where('in_or_out', 1)
                        ->where('carrier', $search_arr['scac']);
               
               
                $count_filter = $data->count();
                $count_total = $count_filter;
                
            } else{
                
                $data = $data->where('in_or_out', 1)
                        ->where('carrier', $request->input('scac'));

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

               
                $received_date = !empty($record['created_at']) ? date("j M Y",strtotime($record['created_at'])) : '';
                if(!empty($record['created_at'])){
                    $received_date = str_replace(" ","&nbsp;",$received_date);
                    $received_date .= '<br> ';
                    $received_date .= date("H:i",strtotime($record['created_at']));
                }

                $js_code = "submit_form(this)";
                $record_file_name = explode('/', $record['data']);
               
                $rec_edi_file_name = "";
                $receive_edi_data = Edi_data::where('id', $record['id'])->get()->collect()->toArray();
                if(isset($receive_edi_data[0]['data']) && !empty($receive_edi_data[0]['data']))
                {
                    $record_rec_file_name = explode('/', $receive_edi_data[0]['data']);
                    if(isset($record_rec_file_name) && count($record_rec_file_name) > 0)
                        $rec_edi_file_name = $record_rec_file_name[count($record_rec_file_name) - 1];
                }

                $status = "Error";

                if($record['compared_with'] > 0 ){
                   $status="Success";
                }else if($record['compared_with'] == 0){
                    $status = "Pending";
                }else{
                    $status = "Error";
                }

                $result[] = array( 
                    "received_date"=> $received_date,
                    "booking_no"=> $record['booking_no'],
                    "ff_no" => str_replace(['BDP', 'bdp'], '', $record['ff_no']),
                    "bn_no" => $record['bn_no'],
                    "carrier" => $record['carrier'],
                    "status" => $status,
                    'received_edi' => "<form method='POST' action='".route('show.file')."' target='_blank'>
                                        <input type='hidden' name='_token' value='".csrf_token()."' />
                                        <input type='hidden' value='".$record['id']."' name='id' />
                                        <input type='hidden' value='incoming' name='type' />
                                        <input type='hidden' value='1' name='file' />
                                        <a href='".route('show.file')."' onclick='event.preventDefault();".$js_code."'>
                                            <i class='material-icons' style='vertical-align:middle;' title='".$rec_edi_file_name.
                                            "'>file_present</i>
                                        </a>
                                    </form>",
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