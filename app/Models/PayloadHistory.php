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

    public function scopeDataChart($query, $statisticParams, $status)
    {
        $from = date('Y-m-d 00:00:00', strtotime($statisticParams['fromDate']));
        $to = date('Y-m-d 23:59:59', strtotime($statisticParams['toDate']));

        return $query
            ->select(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as date'), DB::raw('count(*) as quantity'))
            ->where('status', $status)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('quantity', 'date');
    }

    public function scopeDataChartByUser($query, $statisticParams, $status, $userID)
    {
        $from = date('Y-m-d 00:00:00', strtotime($statisticParams['fromDate']));
        $to = date('Y-m-d 23:59:59', strtotime($statisticParams['toDate']));

        return $query
            ->select(
                DB::raw('DATE_FORMAT(payload_histories.created_at, "%d-%m-%Y") as date'),
                DB::raw('count(*) as quantity')
            )
            ->join('webhooks', 'payload_histories.webhook_id', '=', 'webhooks.id')
            ->where('webhooks.user_id', $userID)
            ->where('payload_histories.status', $status)
            ->whereBetween('payload_histories.created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('quantity', 'date');
    }
}
