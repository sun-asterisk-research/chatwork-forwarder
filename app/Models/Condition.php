<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Condition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'object_id',
        'field',
        'operator',
        'value',
        'object_type',
    ];

    public function object()
    {
        return $this->morphTo();
    }
}
