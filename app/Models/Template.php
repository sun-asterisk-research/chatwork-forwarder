<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'content',
        'params',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
