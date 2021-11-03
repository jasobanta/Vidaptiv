<?php

namespace App\Http\Controllers;

use Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EdiSendCompareEmail extends Controller {

    public function sendCompareEdiEmail($edi_id) {
        try {
            $template = (new EdiEmailController())->ediEmailTemplate($edi_id, 0, 3); //3=Owner Notification template 
            if (!empty($template['data']['email_to'])) {
                $data['email_to'] = !empty($template['data']['email_to']) ? explode(',', $template['data']['email_to']) : '';
                $data['email_cc'] = !empty($template['data']['email_cc']) ? explode(',', $template['data']['email_cc']) : '';
                $data['email_bcc'] = !empty($template['data']['email_bcc']) ? explode(',', $template['data']['email_bcc']) : '';
                $data['subject'] = !empty($template['data']['subject']) ? $template['data']['subject'] : '';
                $data['message'] = !empty($template['data']['message']) ? $template['data']['message'] : '';
                $data['signature'] = !empty($template['data']['signature']) ? $template['data']['signature'] : '';

                $data['email_from_name'] = !empty($template['data']['email_from_name']) ? $template['data']['email_from_name'] : '';
                $data['email_from'] = !empty($template['data']['email_from']) ? $template['data']['email_from'] : '';

                if (!empty($data['email_to'])) {
                    \logEmailsToDatabase([
                        'email_to' => $data['email_to'],
                        'email_cc' => $data['email_cc'],
                        'email_bcc' => $data['email_bcc'],
                        'subject' => $data['subject'],
                        'message' => $data['message'],
                        'signature' => $data['signature'],
                        'user_id' => 0,
                        'form_state' => json_encode($data),
                        'attachement' => [],
                        'view' => ['html' => 'edi.edi_compare_mail'],
                        'email_data' => ['email_body' => $data['message'], 'signature' => $data['signature']],
                        'module' => 'CompareEdi',
                        'module_item_id' => $edi_id,
                        'email_template_id' => 3,
                        'email_from_name' => $data['email_from_name'],
                        'email_from' => $data['email_from'],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: sendCompareEdiEmail(): " . $e->getMessage() . ' Line no' . $e->getLine());
        }
    }

    public function viewEdiDiff($keys) {
        $keys = Crypt::decryptString($keys);
        $data = !empty($keys) ? json_decode($keys, true) : [];

        if (!empty($data['id']) && !empty($data['type'])) {
            return redirect()->route('show.file.action', ['id' => $data['id'], 'type' => $data['type']]);
        }

        Session::flash("message", "Can't access Edi difference.");
        return redirect()->route('/');
    }

}
