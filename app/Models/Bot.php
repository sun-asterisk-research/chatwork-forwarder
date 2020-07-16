<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
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

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId)->get();
    }
}
