<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Interfaces\ChatworkRepositoryInterface;

class ChatworkRepository implements ChatworkRepositoryInterface
{
    public function getRooms($chatwork)
    {
        $rooms = $chatwork->rooms()->list();
        $groupBoxs = [];
        foreach ($rooms as $room) {
            if ($room['type'] == 'group') {
                array_push($groupBoxs, $room);
            }
        }

        return $groupBoxs;
    }
}
