<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use App\Models\CarrierRuleMetaData;

class CarrierSetup extends Model {

    use HasFactory,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'carrier_name',
        'carrier_email',
        'carrier_scac',
        'bdp_owner',
        'ftp_location',
        'ftp_userid',
        'ftp_password',
        'is_ftp',
        'msg_type',
        'reply_via_email',
        'status',
    ];

    public function getReplyViaEmailTitleAttribute($value) {
        return $value == 1 ? "Y" : "N";
    }

    public function getStatusTitleAttribute($value) {
        return $value == 1 ? "Y" : "N";
    }

    public function getFtpPasswordAttribute($value) {
        return $value != '' ? Crypt::decryptString($value) : '';
    }

    public function getCarrierList($status = '') {
        try {
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
                        'status',
                        'status as status_title',
            ]);

            if ($status != '') {
                $rows = $rows->where('status', $status);
            }
            return $rows->orderBy('carrier_name', 'ASC')->get();
        } catch (\Exception $ex) {
            return [];
        }
    }

    public function getCarrierIgnoreRules() {
        return $this->hasMany(CarrierRuleMetaData::class, 'carrier_id', 'id')->where('status', 1)->where('is_ignore',1)->whereNull('deleted_at');
    }

    public function getCarrierCompareElementRules() {
        return $this->hasMany(CarrierRuleMetaData::class, 'carrier_id', 'id')->where('status', 1)->where('is_ignore',0)->whereNotNull('compare_elements')->whereNull('deleted_at');
    }

}
