<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdiTitle extends Model {

    use HasFactory,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'title',
        'status_code',
    ];

    public function getStatusTitleAttribute($value) {
        return $value == 1 ? "Y" : "N";
    }

    public function getStatusList() {
        $edi_status = [];
        try {
            $rows = $this->select([
                        'id',
                        'status_code',
                        'title'
                    ])
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('status_code', 'ASC')
                    ->orderBy('title', 'ASC')
                    ->get();

            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $edi_status[$row->status_code] = $row->title;
                }
            }
        } catch (\Exception $ex) {
            //
        }

        return $edi_status;
    }

    public function getStatusListById() {
        $edi_status = [];
        try {
            $rows = $this->select([
                        'id',
                        'title'
                    ])
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('status_code', 'ASC')
                    ->orderBy('title', 'ASC')
                    ->get();

            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $edi_status[$row->id] = $row->title;
                }
            }
        } catch (\Exception $ex) {
            //
        }

        return $edi_status;
    }
    
}
