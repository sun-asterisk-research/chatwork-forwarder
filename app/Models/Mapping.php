<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    protected $fillable = [
        'webhook_id',
        'name',
        'key',
        'value',
    ];

    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }
}
