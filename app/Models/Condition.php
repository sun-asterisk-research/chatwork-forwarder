<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    protected $fillable = [
        'payload_id',
        'field',
        'operator',
        'value',
    ];
}
