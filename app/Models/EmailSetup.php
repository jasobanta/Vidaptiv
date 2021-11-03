<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailSetup extends Model {

    use HasFactory,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'template_title',
        'status',
        'email_to',
        'email_cc',
        'email_bcc',
        'subject',
        'message',
        'edi_title_id',
        'template_types',
    ];

    public function getStatusTitleAttribute($value) {
        return $value == 1 ? "Y" : "N";
    }

    public function getTypeTitleAttribute($value) {
        $types = $this->emailTemplateTypes();
        return !empty($types[$value]) ? $types[$value] : '';
    }

    public function emailTemplateTypes() {
        return [
            1 => "Carrier Reject",
            2 => "BDP Reject",
            3 => "Owner Notification",
            4 => "Draft Acceptance",
            5 => "Final Acceptance",
        ];
    }

    public function emailTemplateTitles() {
        $return_data = [];
        $rows = self::select([
                    'id',
                    'template_title'
                ])
                ->where('status', 1)
                ->where('hide', 0)
                ->whereNull('deleted_at')
                ->orderBy('id', 'DESC')
                ->get();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $return_data[$row->id] = $row->template_title;
            }
        }
        return $return_data;
    }

    public function emailTemplateVisibility() {
        $return_data = [];
        $rows = self::select([
                    'id',
                    'template_types'
                ])
                ->where('status', 1)
                ->where('hide', 0)
                ->whereNull('deleted_at')
                ->orderBy('id', 'DESC')
                ->get();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $return_data[$row->id] = $row->template_types;
            }
        }
        return $return_data;
    }

}
