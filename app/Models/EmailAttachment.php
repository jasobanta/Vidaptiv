<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmailAttachment extends Model {

    /**
     * The attributes that are guarded or not mass assignable
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

   
    public function email(){
        return $this->belongsTo(Email::class,"email_id");
    }

}
