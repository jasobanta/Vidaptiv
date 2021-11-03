<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailSetup;
use App\Models\EdiTitle;
use Illuminate\Support\Facades\Session;

class EmailController extends Controller {

    public function index(Request $request) {
        $rows = EmailSetup::select([
                    'id',
                    'type_id',
                    'type_id as type_title',
                    'status as status_title',
                    'subject',
                    'template_title',
                    'email_to',
                    'email_cc',
                    'email_bcc',
                    'message',
                    'signature',
                ])->orderBy('id', 'DESC')->get();
        return view('email/list')->with('rows', $rows);
    }

    public function add(Request $request) {
        return view('email/form')
                ->with('types', (new EmailSetup())->emailTemplateTypes())
                ->with('edi_titles', (new EdiTitle())->getStatusListById());
    }

    public function edit($id) {
        $row = EmailSetup::where('id', $id)->first();
        return view('email/form')
                ->with('row', $row)
                ->with('types', (new EmailSetup())->emailTemplateTypes())
                ->with('edi_titles', (new EdiTitle())->getStatusListById());
    }

    public function save(Request $request, $id = 0) {
        $request->validate([
            'template_title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'email_to' => 'required|string|max:255',
            'email_cc' => 'nullable|string|max:255',
            'email_bcc' => 'nullable|string|max:255',
        ]);

        $data = [
            'type_id' => !empty($request->type_id) ? $request->type_id : 0,
            'status' => isset($request->status) ? 1 : 0,
            'template_title' => $request->template_title,
            'subject' => $request->subject,
            'email_to' => $request->email_to,
            'email_cc' => $request->email_cc,
            'email_bcc' => $request->email_bcc,
            'message' => $request->message,
            'signature' => $request->signature,
            'edi_title_id' => !empty($request->edi_title_id) ? $request->edi_title_id : 0,
        ];

        if(!empty($request->template_types)) {
            $template_types = implode(',',$request->template_types);
            $data['template_types'] = $template_types;
        }

        //dd($data);

        if ($id > 0) {
            EmailSetup::where('id', $id)->update($data);
            Session::flash('update_message', 'Email setup is updated!');
        } else {
            EmailSetup::create($data);
            Session::flash('add_message', 'New email setup is added!');
        }

        return redirect()->route('email.list');
    }

    public function delete($id) {
        $email_obj = new EmailSetup();

        if ($email_obj = $email_obj->find($id)) {
            $email_obj->delete();
            Session::flash('delete_message', 'Email setup deleted!');
        }

        Session::flash('delete_error', 'Email setup already deleted!');

        return redirect()->route('email.list');
    }

}
