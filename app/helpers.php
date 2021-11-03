<?php

use App\Models\Email;
use App\Models\EmailAttachment;
use App\Models\Edi_data;
use Illuminate\Support\Facades\Log;

function logEmailsToDatabase($params) {
    $from_name = $sender_name = !empty($params['email_from_name']) ? $params['email_from_name'] : env('MAIL_FROM_NAME');
    $from_email = $sender_email = !empty($params['email_from']) ? $params['email_from'] : env('MAIL_FROM_ADDRESS');

    try {
        $email = Email::create([
                    'email_from' => $from_email,
                    'email_to' => !empty($params['email_to']) ? implode(",", preg_replace('/\s+/', '', $params['email_to'])) : NULL,
                    'email_cc' => !empty($params['email_cc']) ? implode(",", preg_replace('/\s+/', '', $params['email_cc'])) : NULL,
                    'email_bcc' => !empty($params['email_bcc']) ? implode(",", preg_replace('/\s+/', '', $params['email_bcc'])) : NULL,
                    'subject' => $params['subject'],
                    'user_id' => isset($params['user_id']) ? $params['user_id'] : NULL,
                    'form_state' => isset($params['form_state']) ? $params['form_state'] : NULL,
                    'message' => $params['message'],
                    "module" => isset($params['module']) ? $params['module'] : "System",
                    "in_or_out" => isset($params['in_or_out']) ? $params['in_or_out'] : 1,
                    'module_item_id' => isset($params['module_item_id']) ? $params['module_item_id'] : NULL,
                    'email_template_id' => isset($params['email_template_id']) ? $params['email_template_id'] : NULL,
        ]);

        if(isset($params['module_item_id'])){
            Edi_data::find($params['module_item_id'])->increment('sent_emails_count', 1);
        }

        if (isset($params['attachment']) && count($params['attachment']) > 0) {
            foreach ($params['attachment'] as $attachment) {

                if (isset($attachment['file_path'])) {
                    EmailAttachment::create([
                        "email_id" => $email->id,
                        "file_path" => $attachment['file_path'],
                        "mime_type" => isset($attachment['mime_type']) ? $attachment['mime_type'] : NULL,
                    ]);
                } else {
                    Log::info("ERROR: logEmailsToDatabase(): Email attachment does not have a valid file path");
                }
            }
        }
    } catch (\Exception $e) {
        Log::info("ERROR: logEmailsToDatabase(): " . $e->getMessage());
    }

    sendEmailAfterLog([
        'html_msg' => isset($params['html_msg']) ? $params['html_msg'] : "",
        'view' => isset($params['view']) ? $params['view'] : "",
        'email_data' => isset($params['email_data']) ? $params['email_data'] : "",
        'from_name' => $from_name,
        'from_email' => $from_email,
        'sender_name' => $sender_name,
        'sender_email' => $sender_email,
        'to' => $params['email_to'],
        'cc' => isset($params['email_cc']) ? $params['email_cc'] : [],
        'bcc' => isset($params['email_bcc']) ? $params['email_bcc'] : [],
        'subject' => $params['subject'],
        'attachment' => isset($params['attachment']) ? $params['attachment'] : [],
    ]);
}

function sendEmailAfterLog($params) {

    try {
        Mail::send($params['view'], $params['email_data'], function($message)use($params) {

            if (isset($params['from_email'])) {
                $message->from($params['from_email'], $params['from_name']);
            }

            if (isset($params['sender_email'])) {
                $message->sender($params['sender_email'], $params['sender_name']);
            }

            foreach ($params['to'] as $to) {
                $message->to(trim($to));
            }

            if (!empty($params['cc'])) {
                foreach ($params['cc'] as $cc) {
                    $message->cc(trim($cc));
                    $message->replyTo(trim($cc));
                }
            } else {
                $message->replyTo(env('MAIL_REPLY_TO'));
            }

            if (!empty($params['bcc'])) {
                foreach ($params['bcc'] as $bcc) {
                    $message->bcc(trim($bcc));
                }
            }

            if (isset($params['attachment']) && count($params['attachment']) > 0) {
                foreach ($params['attachment'] as $attachment) {
                    $message->attach($attachment['file_path'], ['as' => $attachment['file_name'], 'mime' => $attachment['mime_type']]);
                }
            }

            $message->subject($params['subject']);

            if (isset($params['html_msg']) && $params['html_msg'] != '') {
                $message->setBody($params['html_msg'], 'text/html');
            }
        });
    } catch (\Exception $e) {
        Log::info("ERROR: sendEmailAfterLog(): " . $e->getMessage());
    }
}
