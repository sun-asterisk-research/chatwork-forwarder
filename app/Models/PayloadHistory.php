<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Enums\PayloadHistoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayloadHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'webhook_id',
        'params',
        'status',
        'log',
    ];

    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }

    public function messageHistories()
    {
        return $this->hasMany(MessageHistory::class);
    }

    public function scopeSearch($query, $searchParams, $perPage)
    {
        $status = PayloadHistoryStatus::toArray();
        if ($searchParams['webhook'] && $searchParams['status']) {
            return $query
                ->where('payload_histories.webhook_id', $searchParams['webhook'])
                ->where('payload_histories.status', $status[$searchParams['status']])
                ->paginate($perPage);
        } elseif ($searchParams['webhook']) {
            return $query
                ->where('payload_histories.webhook_id', $searchParams['webhook'])
                ->paginate($perPage);
        } elseif ($searchParams['status']) {
            return $query
                ->where('payload_histories.status', $status[$searchParams['status']])
                ->paginate($perPage);
        } else {
            return $query->paginate($perPage);
        }
    }

    public function delete()
    {
        DB::transaction(function () {
            $this->messageHistories()->delete();
            parent::delete();
        });
    }
}
