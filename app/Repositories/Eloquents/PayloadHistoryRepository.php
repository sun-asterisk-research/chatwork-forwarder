<?php
namespace App\Repositories\Eloquents;

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
}
