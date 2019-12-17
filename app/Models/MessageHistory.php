<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageHistory extends Model
{
    use SoftDeletes;

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
