<?php

namespace App\Repositories\Eloquents;

use App\Models\MessageHistory;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\MessageHistoryRepositoryInterface;

class MessageHistoryRepository extends BaseRepository implements MessageHistoryRepositoryInterface
{
    public function getModel()
    {
        return MessageHistory::class;
    }

    public function getAllAndSearch($id, $keyword)
    {
        $perPage = config('paginate.perPage');
        $query = $this->model->where('payload_history_id', $id)->orderBy('status', 'desc')
                        ->orderBy('created_at', 'desc');
        if (!empty($keyword)) {
            return $query->where('message_content', 'LIKE', "%$keyword%")
                        ->paginate($perPage);
        } else {
            return $query->paginate($perPage);
        }
    }
}
