<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MessageHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payload_history_id',
        'message_content',
        'status',
        'log',
    ];

    public function payloadHistory()
    {
        return $this->belongsTo(PayloadHistory::class);
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
                DB::raw('DATE_FORMAT(message_histories.created_at, "%d-%m-%Y") as date'),
                DB::raw('count(*) as quantity')
            )
            ->join('payload_histories', 'message_histories.payload_history_id', '=', 'payload_histories.id')
            ->join('webhooks', 'payload_histories.webhook_id', '=', 'webhooks.id')
            ->where('webhooks.user_id', $userID)
            ->where('message_histories.status', $status)
            ->whereBetween('message_histories.created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('quantity', 'date');
    }
}
