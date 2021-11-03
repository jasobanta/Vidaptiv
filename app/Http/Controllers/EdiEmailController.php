<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\EdiTitle;
use App\Models\Edi_data;
use App\Models\EmailSetup;
use App\Models\Edi_meta_data;
use App\Http\Controllers\EdiController;
use Mail;
use App\Http\Controllers\EdiSendCompareEmail;
use Illuminate\Support\Facades\Crypt;
use App\Models\CarrierSetup;
use Illuminate\Support\Facades\Log;

class EdiEmailController extends Controller {

    public function compareColumns() {
        return ['booking_no', 'bn_no', 'bl_no', 'ff_no', 'carrier', 'owner_email', 'carrier_email', 'click_here'];
    }

    public function emailForm($id) {
        $template_types = (new EmailSetup())->emailTemplateTitles();
        $template_rules = (new EmailSetup())->emailTemplateVisibility();

        $row = DB::table('edi_datas as edi')
                        ->leftJoin('carrier_setups as carrier', 'edi.carrier', '=', 'carrier.carrier_scac')
                        ->select('edi.id', 'edi.owner_email', 'edi.compared_with', 'edi.carrier', 'edi.data')
                        ->where('edi.id', '=', $id)->first();

        $edidata = $AllReqData = (new EdiController)->getEditDetailsWithDiff($id);

        $diff_rows = (new EdiController())->diffRows($id, $row->carrier, $accept_ignore = 0, $edidata['edi']->data, $edidata['compare_to_data']->data);
        $metas = Edi_meta_data::where('edi_data_id', $id)->get()->collect()->toArray();

        $metaFormated = [];
        $metaFormated_carrier_reject = [];
        $metaFormated_bdp_reject = [];

        foreach ($metas as $meta) {
            $metaFormated[] = $meta['rules_id'];

            if ($meta['is_carrier_reject']) {
                $metaFormated_carrier_reject[] = $meta['rules_id'];
            }
            if ($meta['is_bdp_reject']) {
                $metaFormated_bdp_reject[] = $meta['rules_id'];
            }
        }

        $rule_ids = [];
        foreach ($diff_rows['diff_rule_id'] as $rule_id) {
            if (in_array($rule_id, $metaFormated_carrier_reject)) {
                $rule_ids[] = $rule_id;
            }
        }
        $metaFormated_carrier_reject = $rule_ids;

        $rule_ids = [];
        foreach ($diff_rows['diff_rule_id'] as $rule_id) {
            if (in_array($rule_id, $metaFormated_bdp_reject)) {
                $rule_ids[] = $rule_id;
            }
        }

        $metaFormated_bdp_reject = $rule_ids;

        return view('edi/email_form')->with('row', $row)
                        ->with(compact('template_types'))
                        ->with(compact('template_rules'))
                        ->with(compact('edidata'))
                        ->with(compact('metaFormated_carrier_reject'))
                        ->with(compact('metaFormated_bdp_reject'))
                        ->with(compact('metas'))
                        ->with(compact('metaFormated'))
                        ->with(compact('diff_rows'));
    }

    public function actionForm(Request $request) {
        $search = [
            'rules_id' => $request->ruleId,
            'edi_data_id' => $request->ediId
        ];
        if ($request->actionId == 1) {
            $search['is_accepted'] = 1;
        }
        if ($request->actionId == 2) {
            $search['is_carrier_reject'] = 1;
        }
        if ($request->actionId == 3) {
            $search['is_bdp_reject'] = 1;
        }

        $data = Edi_meta_data::firstOrNew($search);
        collect($data)->toArray();
        if ($request->label) {
            $data->label = $request->label;
        }
        return view('edi.actionForm')->with(compact('data'));
    }

    // accept all segments
    public function saveAcceptAction(Request $request) {
        $message = [];
        $ruleIds = $request->rules;
        $edi_data_id = $request->ediId;
        foreach ($ruleIds as $rules_id) {
            $search = [
                'rules_id' => $rules_id,
                'edi_data_id' => $edi_data_id,
            ];
            $metaAction = Edi_meta_data::firstOrNew($search);
            $userid = auth()->user()->id;
            $metaAction->user_id = $userid;
            $metaAction->reason_code = 0; // use this when we implement reason code 
            $metaAction->is_accepted = 1; // use this when we implement reason code 
            $metaAction->save();
            $message['data'][$rules_id] = $metaAction;
        }
        $message['status'] = 200;
        return response()->json($message);
    }

    public function saveediAction(Request $request) {
        $search = [
            'rules_id' => $request->rules_id,
            'edi_data_id' => $request->edi_data_id
        ];
        $rules = [];
        if ($request->is_accepted == 1) {
            $search['is_accepted'] = 1;
        }

        //need to delete
        if (!empty($request->meta_data_id)) {
            if ($request->is_carrier_reject == 1 && empty($request->carrier_reject_msg)) {
                $delete = true;
            } elseif ($request->is_bdp_reject == 1 && empty($request->bdp_reject_msg)) {
                $delete = true;
            }
            if (!empty($delete)) {
                Edi_meta_data::destroy($request->meta_data_id);
                return response()->json([
                            'action' => 'delete',
                            'status' => true
                ]);
            }
        }

        if ($request->is_carrier_reject == 1) {
            $search['is_carrier_reject'] = 1;
            $rules = [
                'carrier_reject_msg' => 'required',
            ];
        }

        if ($request->is_bdp_reject == 1) {
            $search['is_bdp_reject'] = 1;
            $rules = [
                'bdp_reject_msg' => 'required',
            ];
        }

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            $data['action'] = 'add';
            $data['status'] = false;
            $data['message'] = "VALIDATION";
            $data['data'] = $v->errors();
            return response()->json($data);
        }

        $metaAction = Edi_meta_data::firstOrNew($search);

        $userid = auth()->user()->id;
        $metaAction->user_id = $userid;
        $metaAction->reason_code = 0; // use this when we implement reason code 
        if ($request->is_carrier_reject == 1) {
            $metaAction['carrier_reject_msg'] = $request->carrier_reject_msg;
        }
        if ($request->is_bdp_reject == 1) {
            $metaAction['bdp_reject_msg'] = $request->bdp_reject_msg;
        }

        $data = $metaAction->save();
        $data = collect($data)->toArray();
        $data['status'] = 200;
        //dd($data);
        return response()->json($data);
    }

    public function sendEmail(Request $request) {
        $rules = [
            'email_to' => 'required',
            'subject' => 'required'
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            $data['status'] = false;
            $data['message'] = "VALIDATION";
            $data['data'] = $v->errors();
            return response()->json($data);
        }

        $userid = auth()->id();

        try {
            $edi_id = $request->edi_data_id;

            $template = EmailSetup::where('status', 1)->first();
            $metas_rows = Edi_meta_data::where('edi_data_id', $edi_id)->get()->collect()->toArray();
            $AllReqData = (new EdiController)->getEditDetailsWithDiff($edi_id);

            $edi_compare_row = Edi_data::select(['id', 'data'])->where('id', $AllReqData['edi']['compared_with'])->with('getReceivedDate')->first();

            $diff_rows = (new EdiController())->diffRows($AllReqData['edi']['carrier'], $AllReqData['edi']->data, $edi_compare_row['data']);

            $metaFormated = [];
            $metaFormated_carrier_reject = [];
            $metaFormated_bdp_reject = [];

            $metas = [];
            if (!empty($metas_rows) && !empty($diff_rows['diff_rule_id'])) {
                foreach ($metas_rows as $meta) {
                    if (in_array($meta['rules_id'], $diff_rows['diff_rule_id'])) {
                        $metas[$meta['id']] = [
                            'rules_id' => $meta['rules_id'],
                            'reason_code' => $meta['reason_code'],
                            'is_accepted' => $meta['is_accepted'],
                            'is_carrier_reject' => $meta['is_carrier_reject'],
                            'is_bdp_reject' => $meta['is_bdp_reject'],
                            'carrier_reject_msg' => $meta['carrier_reject_msg'],
                            'bdp_reject_msg' => $meta['bdp_reject_msg'],
                        ];

                        $metaFormated[] = $meta['rules_id'];

                        if ($meta['is_carrier_reject']) {
                            $metaFormated_carrier_reject[] = $meta['rules_id'];
                        }
                        if ($meta['is_bdp_reject']) {
                            $metaFormated_bdp_reject[] = $meta['rules_id'];
                        }
                    }
                }
            }

            $AllReqData['diff_rows'] = $diff_rows;
            $AllReqData['intent'] = $request->email_type;
            $AllReqData['to'] = !empty($request->email_to) ? explode(',', $request->email_to) : [];
            $AllReqData['cc'] = !empty($request->email_cc) ? explode(',', $request->email_cc) : [];
            $AllReqData['subject'] = $request->subject;
            $AllReqData['message'] = $request->message;
            $AllReqData['signature'] = $request->signature;
            $AllReqData['metas'] = $metas;
            $AllReqData['metaFormated'] = $metaFormated;
            $AllReqData['metaFormated_carrier_reject'] = $metaFormated_carrier_reject;
            $AllReqData['metaFormated_bdp_reject'] = $metaFormated_bdp_reject;
            $AllReqData['template'] = $template;

            $email = [
                'email_to' => $AllReqData['to'],
                'email_cc' => $AllReqData['cc'],
                'subject' => $AllReqData['subject'],
                'message' => $AllReqData['message'],
                'signature' => $AllReqData['signature'],
                'user_id' => $userid,
                'form_state' => json_encode($AllReqData),
                'view' => ['html' => 'edi.sendmail'],
                'email_data' => ['AllReqData' => $AllReqData, 'template' => $template],
                'module' => 'sendEmail',
                'attachment' => [],
                'module_item_id' => $edi_id,
                'email_template_id' => $request->edi_title_id,
                'email_from_name' => $AllReqData['edi']['owner_name'],
                'email_from' => $AllReqData['edi']['owner_email'],
            ];


            $html = view('edi.sendmail')->with(compact('AllReqData'))->render();

            if (isset($request->attachmentEnabled)) {
                $AllReqData['attachment_path'] = $request->email_attachment_path;
                $AllReqData['attachment_name'] = $request->email_attachment_name;
                $AllReqData['attachment_mimetype'] = $request->email_attachment_mimetype;
                $AllReqData['attachment_enabled'] = 1;

                $email['attachment'][] = [
                    "file_name" => $AllReqData['attachment_name'],
                    "file_path" => $AllReqData['attachment_path'],
                    "mime_type" => $AllReqData['attachment_mimetype'],
                ];
            }

            $email_subject = strtolower($email['subject']);
            $email['html_msg'] = '';
            if (strpos($email_subject, 'reject') === false) {
                $email['view'] = [];
                $email['email_data'] = [];
                $email['html_msg'] = str_replace("\r\n", "<br>", $email['message']);
            }
            $edi_title = EdiTitle::where('id', '=', $request->edi_title_id)->first();

            if (isset($edi_title->status_code)) {
                Edi_data::where('id', $edi_id)->update(
                        array(
                            'status' => $edi_title->status_code,
                        )
                );
            }
            \logEmailsToDatabase($email);

            $data['status'] = 1;
            $data['message'] = "SUCCESS";
            $data['data'] = [];
            return $data;
        } catch (\Exception $e) {
            $data['status'] = 0;
            $data['message'] = "ERROR";

            Log::info("ERROR: sendEmail(): " . $e->getMessage());
            return $data['data'] = [];
        }
    }

    public function downloadForm(Request $request) {
        $edi_id = $request->id;
        return view('edi.downloadForm')->with(compact('edi_id'));
    }

    public function ediEmailTemplate($edi_id, $template_id = 0, $type_id = 0) {
        try {
            $edi_row = Edi_data::select(
                            [
                                'id',
                                'compared_with',
                                'booking_no',
                                'bn_no',
                                'ff_no',
                                'carrier',
                                'owner_name',
                                'owner_email'
                            ]
                    )->where('id', $edi_id)->with('getReceivedDate')->first();
            if (!empty($edi_row)) {
                $default_email = isset($edi_row['carrier']) ? $edi_row['carrier'] : '';
                $carrier_sec = isset($edi_row['carrier']) ? $edi_row['carrier'] : '';

                $carrier_email_address = CarrierSetup::select('carrier_email')->where('carrier_scac', '!=', '')->where('carrier_scac', $carrier_sec)->pluck('carrier_email')->first();

                //Get Carrier email
                $edi_data = collect($edi_row)->toArray();
                $carrier_row = $edi_data['get_received_date'];

                $data_row = [
                    'id' => isset($edi_row['id']) ? $edi_row['id'] : 0,
                    'booking_no' => isset($edi_row['booking_no']) ? $edi_row['booking_no'] : '',
                    'bn_no' => isset($edi_row['bn_no']) ? $edi_row['bn_no'] : '',
                    'bl_no' => isset($edi_row['bn_no']) ? $edi_row['bn_no'] : '',
                    'ff_no' => isset($edi_row['ff_no']) ? $edi_row['ff_no'] : '',
                    'carrier' => isset($edi_row['carrier']) ? $edi_row['carrier'] : '',
                    'owner_email' => !empty($edi_row['owner_email']) ? $edi_row['owner_email'] : $carrier_email_address,
                    'carrier_email' => !empty($carrier_row['owner_email']) ? $carrier_row['owner_email'] : $carrier_email_address,
                        //'carrier_email' => $carrier_email_address, //temporary fixed
                ];

                if (!empty($edi_row['id'])) {
                    $link_key = Crypt::encryptString(json_encode(['id' => $edi_row['id'], 'type' => 'diff']));
                    $data_row['click_here'] = "<a href='" . url('/view-edi-diff', $link_key) . "' target='_blank'>Click Here</a>";
                    $data_row['url'] = url('/view-edi-diff', $link_key);
                }

                foreach ($data_row as $index => $data) {
                    if (in_array($index, $this->compareColumns())) {
                        if ($index == 'owner_email' && $data == '') {
                            $data = $default_email;
                        }
                        $replace['##' . $index . '##'] = $data;
                    }
                }

                if ($type_id > 0) {
                    $template_row = EmailSetup::where('type_id', $type_id)->first();
                } else {
                    $template_row = EmailSetup::where('id', $template_id)->first();
                }

                $email_to = str_replace(array_keys($replace), array_values($replace), $template_row['email_to']);
                $email_cc = str_replace(array_keys($replace), array_values($replace), $template_row['email_cc']);
                $email_bcc = str_replace(array_keys($replace), array_values($replace), $template_row['email_bcc']);
                $subject = str_replace(array_keys($replace), array_values($replace), $template_row['subject']);
                $message = str_replace(array_keys($replace), array_values($replace), $template_row['message']);
                $signature = str_replace(array_keys($replace), array_values($replace), $template_row['signature']);

                $valid_email_obj = new EdiSendCompareEmail();

                $email_to = !empty($email_to) ? $this->getValidEmails($email_to) : '';
                $email_cc = !empty($email_cc) ? $this->getValidEmails($email_cc) : '';
                $email_bcc = !empty($email_bcc) ? $this->getValidEmails($email_bcc) : '';
                $edi_title_id = $template_row['edi_title_id'];

                return [
                    'status' => true,
                    'message' => 'SUCCESS',
                    'data' => [
                        'email_to' => !empty($email_to) ? implode(',', $email_to) : '',
                        'email_cc' => !empty($email_cc) ? implode(',', $email_cc) : '',
                        'email_bcc' => !empty($email_bcc) ? implode(',', $email_bcc) : '',
                        'subject' => $subject,
                        'message' => $message,
                        'signature' => $signature,
                        'email_from_name' => isset($edi_row['owner_name']) ? $edi_row['owner_name'] : '',
                        'email_from' => isset($edi_row['owner_email']) ? $edi_row['owner_email'] : '',
                    ]
                ];
            }
        } catch (\Exception $ex) {
            return [
                'status' => false,
                'message' => 'SERVER_ERROR',
                'data' => [],
            ];
        }
    }

    public function getValidEmails($emails = '') {
        if (!is_array($emails)) {
            $emails = explode(',', $emails);
        }

        $new_email = [];
        if (!empty($emails)) {
            foreach ($emails as $email) {
                if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    $new_email[] = trim($email);
                }
            }
        }

        return $new_email;
    }

    public function sendEmailStoreEDIFile($data = []) {
        try {
            $to_emails = !empty($data['to_emails']['address']) ? explode(',', $data['to_emails']['address']) : [];
            $data['to_emails'] = $this->getValidEmails($to_emails);

            if (!empty($data['to_emails'])) {
                Mail::send(['html' => 'email.edi_store_message_file'], ['data' => $data], function($m)use($data) {
                    $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    $m->sender(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    foreach ($data['to_emails'] as $to_email) {
                        $m->to($to_email);
                    }
                    $m->subject($data['subject']);
                });
            }
        } catch (\Exception $e) {
            //
        }
    }

}
