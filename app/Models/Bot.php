<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'cw_id',
        'bot_key',
    ];

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
