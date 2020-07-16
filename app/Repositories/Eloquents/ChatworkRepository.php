<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Interfaces\ChatworkRepositoryInterface;

class ChatworkRepository implements ChatworkRepositoryInterface
{
    public function getRooms($chatwork, $type)
    {
        $rooms = $chatwork->rooms()->list();
        $groupBoxs = [];
        foreach ($rooms as $room) {
            $room['name'] = htmlspecialchars($room['name']);
            if ($room['type'] == $type || $type == 'all') {
                array_push($groupBoxs, $room);
            }
        }

        return $groupBoxs;
    }
}
