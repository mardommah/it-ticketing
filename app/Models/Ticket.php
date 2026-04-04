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
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }
}
