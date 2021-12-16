<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bot extends Model
{
    use SoftDeletes;

    public const TYPE_CHATWORK = 'chatwork';
    public const TYPE_SUN_PROXY = 'sun_proxy';
    public const TYPE_SLACK = 'slack';
    public const USE_DEFAULT_SLACK_BOT = 'on';


    protected $fillable = [
        'user_id',
        'name',
        'type',
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

    public function getProxyUri()
    {
        if ($this->type == self::TYPE_SUN_PROXY && isset(config('cw.base_url')[$this->type])) {
            return config('cw.base_url')[$this->type];
        }

        return null;
    }

    public function getAPIOptions()
    {
        if ($baseUri = $this->getProxyUri()) {
            return [
                'base_uri' => $baseUri,
            ];
        }

        return [];
    }
}
