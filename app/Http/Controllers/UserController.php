<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\UserCarrier;
use App\Models\CarrierSetup;
use App\Models\Country;

class UserController extends Controller {

    public function index(Request $request) {
        $rows = User::select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.is_all_carriers as is_all_carriers_title',
                    'users.is_admin as is_admin_title',
                    'users.active as active_title', 
                    'countries.country_code',
                ])->join('countries','users.country_id','=','countries.id', 'left')->orderBy('id', 'DESC')->get();
        return view('user/list')->with('rows', $rows);
    }

    public function add(Request $request) {
        $carriers = ( new CarrierSetup())->getCarrierList('');
        $country_rows = Country::select([
            'id',
            'country_code',
            'country_name'
        ])->where('status', 1)->where('country_code', '!=', '')->orderBy('country_code', 'ASC')->get();
        return view('user/form')->with('carriers', $carriers)->with('all_countries', $country_rows);
    }

    public function edit($id) {
        $carriers = ( new CarrierSetup())->getCarrierList('');
        $user_carrier_ids = ( new UserCarrier())->getCarrierIds($id);
        $row = User::where('id', $id)->first();
        
        $country_rows = Country::select([
            'id',
            'country_code',
            'country_name'
        ])->where('status', 1)->where('country_code', '!=', '')->orderBy('country_code', 'ASC')->get();
        
        return view('user/form')->with('row', $row)->with('carriers', $carriers)->with('user_carrier_ids', $user_carrier_ids)->with('all_countries', $country_rows);
    }

    public function save(Request $request, $id = 0) {
        $is_admin = isset($request->is_admin) ? 1 : 0;

        if (!empty($id)) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            ];
            $data = [
                'is_admin' => $is_admin,
                'active' => isset($request->active) ? 1 : 0,
                'name' => $request->name,
                'email' => $request->email,
                'country_id'=> $request->country_id
            ];

            if (!empty($request->password)) {
                $password = Hash::make($request->password);

                $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
                $data['password'] = $password;
                $data['api_token'] = $password;
            }
        } else {

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ];

            $password = Hash::make($request->password);
            $data = [
                'is_admin' => $is_admin,
                'active' => isset($request->active) ? 1 : 0,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password,
                'api_token' => $request->password,
                'country_id'=> $request->country_id
            ];
        }

        $request->validate($rules);

        if ($id > 0) {
            User::where('id', $id)->update($data);
            Session::flash('update_message', 'User is updated!');
        } else {
            $insert = User::create($data);
            $id = $insert->id;
            Session::flash('add_message', 'New user is added!');
        }

        $user_carrier_obj = new UserCarrier();
        $user_carrier_obj->where('user_id', $id)->update(['status' => 0, 'deleted_at' => now()]);

        if ($is_admin == 1) {
            $is_all_carriers = 1;
        } else {
            $is_all_carriers = isset($request->is_all_carriers) ? 1 : 0;
        }

        (new User())->where('id', $id)->update(['is_all_carriers' => $is_all_carriers, 'country_id'=>$request->country_id]);

        if (!empty($id) && !empty($request->user_carrier_ids) && $is_all_carriers != 1) {
            foreach ($request->user_carrier_ids as $carrier_id) {
                $data_carrier[] = [
                    'status' => 1,
                    'user_id' => $id,
                    'carrier_id' => $carrier_id,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }

            $user_carrier_obj->insert($data_carrier);
        }

        return redirect()->route('users');
    }

    public function delete($id) {
        $obj = new User();

        if ($obj = $obj->find($id)) {
            $obj->delete();
            Session::flash('delete_message', 'User is deleted!');
        }

        Session::flash('delete_error', 'User already deleted!');

        return redirect()->route('users');
    }

}
