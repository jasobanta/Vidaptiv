<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model {

    /**
     * The attributes that are guarded or not mass assignable
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

   
    public function attachment(){
        return $this->hasOne(EmailAttachment::class);
    }

    public function user(){
        return $this->hasOne(User::class,"id","user_id");
    }

}
