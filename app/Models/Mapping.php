<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mapping extends Model
{
    use SoftDeletes;

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

    public function scopeByKey($query, $key)
    {
        return $query->where('mappings.key', '=', $key);
    }
}
