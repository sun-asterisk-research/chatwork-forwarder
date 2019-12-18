<?php

namespace App\Repositories\Eloquents;

use Auth;
use App\Models\PayloadHistory;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\PayloadHistoryRepositoryInterface;

class PayloadHistoryRepository extends BaseRepository implements PayloadHistoryRepositoryInterface
{
    public function getModel()
    {
        return PayloadHistory::class;
    }

    public function find($id)
    {
        return $this->model->with('webhook')->find($id);
    }

    public function getAllByUserAndSearch($perPage, $searchParams)
    {
        $query = PayloadHistory::whereHas('webhook', function ($wh) {
            $wh->where('user_id', Auth::id());
        })->orderBy('payload_histories.created_at', 'desc');
        if ($searchParams) {
            $searchParams = $this->handleSearchParams(['webhook', 'status'], $searchParams);

            return $query->search($searchParams, $perPage);
        } else {
            return $query->paginate($perPage);
        }
    }

    public function getAllAndSearch($perPage, $searchParams)
    {
        $query = PayloadHistory::orderBy('payload_histories.created_at', 'desc');
        if ($searchParams) {
            $searchParams = $this->handleSearchParams(['webhook', 'status'], $searchParams);
            
            return $query->search($searchParams, $perPage);
        } else {
            return $query->paginate($perPage);
        }
    }
}
