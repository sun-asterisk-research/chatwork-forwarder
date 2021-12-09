<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payload extends Model
{
    use SoftDeletes;

    const TYPE_TEXT = 'text';
    const TYPE_BLOCKS = 'blocks';
    const TYPE = ['text', 'blocks'];

    protected $fillable = [
        'webhook_id',
        'content_type',
        'content',
        'params',
    ];

    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }

    public function conditions()
    {
        return $this->morphMany(Condition::class, 'object');
    }
}
