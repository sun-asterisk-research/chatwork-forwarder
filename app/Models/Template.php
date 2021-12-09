<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    const TYPE_TEXT = 'text';
    const TYPE_BLOCKS = 'blocks';
    const TYPE = ['text', 'blocks'];

    protected $fillable = [
        'user_id',
        'name',
        'content_type',
        'content',
        'params',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $searchParams, $perPage)
    {
        return $query
            ->where('name', 'LIKE', '%' . $searchParams['name'] . '%')
            ->where('status', 'LIKE', '%' . $searchParams['status'] . '%')
            ->paginate($perPage);
    }

    public function conditions()
    {
        return $this->morphMany(Condition::class, 'object');
    }
}
