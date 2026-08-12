<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'cityzen_id',
        'subject',
        'text',
        'recepient',
        'status',
        'sent_date',
        'from',
        'from_name',
        'type'
    ];

    public static $sendEmailTemplates = [
    ];

    public function cityzen()
    {
        return $this->belongsTo('App\Cityzen');
    }
}
