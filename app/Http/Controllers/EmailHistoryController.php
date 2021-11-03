<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use Illuminate\Support\Facades\Session;

class EmailHistoryController extends Controller {

    public function index(Request $request) {
        $rows = Email::select(
            "id",
            "user_id",
            "email_cc",
            "email_bcc",
            "email_from",
            "email_to",
            "subject",
            "message",
            "module",
            "created_at"
        )->orderByDesc('created_at')->get();
        return view('emailhistory/list')->with('rows', $rows);
    }

    public function formstate($id) {
        $row = Email::select("form_state")->where('id', $id)->first();
        return response()->json($row);
    }

    public function showById($id,$hash) {
        
        if($hash == md5($id)){
            $rows = Email::select(
                "id",
                "user_id",
                "email_cc",
                "email_bcc",
                "email_from",
                "email_to",
                "subject",
                "message",
                "module",
                "created_at"
            )->where('module_item_id', $id)->orderByDesc('id')->get();
            return view('emailhistory/list')->with('rows', $rows);
        }else{
            abort(401);
        }
    }
}
