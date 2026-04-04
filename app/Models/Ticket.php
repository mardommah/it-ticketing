<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'whatsapp_id',
        'from',
        'participant',
        'reporter_name',
        'message',
        'status',
        'assigned_to',
        'category',
        'whatsapp_timestamp',
    ];
}
