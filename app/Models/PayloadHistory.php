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
}
