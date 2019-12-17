<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payload extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'webhook_id',
        'content',
        'params',
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
