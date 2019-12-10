<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageHistory extends Model
{
    protected $fillable = [
        'payload_history_id',
        'message_content',
        'status',
        'log',
    ];

    public function payloadHistory()
    {
        return $this->belongsTo(PayloadHistory::class);
    }
}
