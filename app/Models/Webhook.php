<?php

namespace App\Models;

use App\Enums\WebhookStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bot_id',
        'name',
        'token',
        'status',
        'description',
        'room_id',
        'room_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }

    public function payloads()
    {
        return $this->hasMany(Payload::class);
    }

    public function mappings()
    {
        return $this->hasMany(Mapping::class);
    }

    public function payloadHistories()
    {
        return $this->hasMany(PayloadHistory::class);
    }

    public function scopeEnable($query)
    {
        return $query->where('status', WebhookStatus::ENABLED);
    }

    public function scopeDisable($query)
    {
        return $query->where('status', WebhookStatus::DISABLED);
    }

    public function scopeSearch($query, $searchParams, $perPage)
    {
        return $query
            ->where('name', 'LIKE', '%' . $searchParams['name'] . '%')
            ->where('status', 'LIKE', '%' . $searchParams['status'] . '%')
            ->paginate($perPage);
    }

    public function scopeByUser($query, $userID)
    {
        return $query->where('user_id', $userID);
    }
}
