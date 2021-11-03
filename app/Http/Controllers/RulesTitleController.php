<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\RulesTitle;

class RulesTitleController extends Controller {

    public function index(Request $request) {
        $rows = RulesTitle::select([
                    'id',
                    'title',
                    'status_code',
                    'status as status_title'
                ])->orderBy('status_code', 'ASC')
                ->orderBy('title', 'ASC')
                ->get();
        return view('rules/title_list')->with('rows', $rows);
    }

    public function add(Request $request) {
        return view('rules/title_form');
    }

    public function edit($id) {
        $row = RulesTitle::where('id', $id)->first();
        return view('rules/title_form')->with('row', $row);
    }

    public function save(Request $request, $id = 0) {
        $request->validate([
            'title' => 'required|string|max:255',
            'status_code' => 'required|numeric'
        ]);

        $data = [
            'title' => $request->title,
            'status_code' => $request->status_code,
            'status' => !empty($request->status) ? 1 : 0,
        ];

        if ($id > 0) {
            RulesTitle::where('id', $id)->update($data);
            Session::flash('update_message', 'EDI title is updated!');
        } else {
            RulesTitle::create($data);
            Session::flash('add_message', 'New EDI title is added!');
        }

        return redirect()->route('rules-title.list');
    }

    public function delete($id) {
        $obj = new RulesTitle();

        if ($obj = $obj->find($id)) {
            $obj->delete();
            Session::flash('delete_message', 'EDI Title is deleted!');
        }

        Session::flash('delete_error', 'EDI title already deleted!');

        return redirect()->route('rules-title.list');
    }

}
