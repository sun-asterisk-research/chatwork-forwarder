<?php
namespace App\Repositories\Eloquents;

use App\Models\Payload;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\PayloadRepositoryInterface;

class PayloadRepository extends BaseRepository implements PayloadRepositoryInterface
{
    public function getModel()
    {
        return Payload::class;
    }
}
