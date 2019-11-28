<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payload extends Model
{
    protected $fillable = [
        'webhook_id',
        'content',
    ];
    
    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }

    public function conditions()
    {
        return $this->hasMany(Condition::class);
    }
}
