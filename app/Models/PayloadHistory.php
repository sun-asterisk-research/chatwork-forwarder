<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayloadHistory extends Model
{
    protected $fillable = [
        'webhook_id',
        'params',
        'status',
        'log',
    ];

    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }

    public function messageHistories()
    {
        return $this->hasMany(MessageHistory::class);
    }
}
