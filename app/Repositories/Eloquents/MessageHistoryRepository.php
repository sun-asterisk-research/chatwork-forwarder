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
}
