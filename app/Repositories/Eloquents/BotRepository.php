<?php

namespace App\Repositories\Eloquents;

use Auth;
use App\Models\Bot;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\BotRepositoryInterface;

class BotRepository extends BaseRepository implements BotRepositoryInterface
{
    public function getModel()
    {
        return Bot::class;
    }

    public function getAllByUser($perPage)
    {
        return Auth::user()->bots()
                            ->orderBy('bots.created_at', 'desc')
                            ->paginate($perPage);
    }
}
