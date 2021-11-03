<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class EmailAttachmentController extends Controller {

    public function download(Request $request) {
        return response()->download($request->path);
    }
}
