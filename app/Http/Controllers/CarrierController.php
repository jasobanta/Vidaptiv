<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\CarrierSetup;
use Illuminate\Support\Facades\Crypt;

class CarrierController extends Controller {

    public function index(Request $request) {
        $rows = CarrierSetup::select([
                    'id',
                    'carrier_name',
                    'carrier_email',
                    'carrier_scac',
                    'ftp_location',
                    'ftp_userid',
                    'is_ftp',
                    'folder_type',
                    'folder_location',
                    'reply_via_email as reply_via_email_title',
                    'status as status_title',
                ])->orderBy('carrier_scac', 'ASC')->get();
        return view('carrier/list')->with('rows', $rows);
    }

    public function add(Request $request) {
        return view('carrier/form');
    }

    public function edit($id) {
        $row = CarrierSetup::where('id', $id)->first();
        return view('carrier/form')->with('row', $row);
    }

    public function save(Request $request, $id = 0) {

        $rules = [
            'carrier_name' => 'required|string|max:255',
            //'carrier_email' => 'required|string|email|max:255'
        ];
        if ($request->is_ftp == 1) {
            $rules['ftp_location'] = 'required|string|max:255';
            $rules['ftp_userid'] = 'required|string|max:255';
            $rules['ftp_password'] = 'required|string|max:255';
        }
        $request->validate($rules);

        $data = [
            'carrier_name' => $request->carrier_name,
            'carrier_email' => $request->carrier_email,
            'carrier_scac' => $request->carrier_scac,
            'bdp_owner' => $request->bdp_owner,
            'ftp_location' => $request->ftp_location,
            'ftp_userid' => $request->ftp_userid,
            'ftp_password' => Crypt::encryptString($request->ftp_password),
            'is_ftp' => !empty($request->is_ftp) ? 1 : 0,
            'folder_type' => $request->folder_type,
            'folder_location' => !empty($request->folder_location) ? $request->folder_location : $request->carrier_scac,
            'reply_via_email' => !empty($request->reply_via_email) ? 1 : 0,
            'status' => !empty($request->status) ? 1 : 0,
        ];

        if ($id > 0) {
            CarrierSetup::where('id', $id)->update($data);
            Session::flash('update_message', 'Carrier setup is updated!');
        } else {
            CarrierSetup::create($data);
            Session::flash('add_message', 'New carrier setup is added!');
        }

        return redirect()->route('carrier.list');
    }

    public function delete($id) {
        $carrier_obj = new CarrierSetup();

        if ($carrier_obj = $carrier_obj->find($id)) {
            $carrier_obj->delete();
            Session::flash('delete_message', 'Carrier setup deleted!');
        }

        Session::flash('delete_error', 'Carrier setup already deleted!');

        return redirect()->route('carrier.list');
    }

}
