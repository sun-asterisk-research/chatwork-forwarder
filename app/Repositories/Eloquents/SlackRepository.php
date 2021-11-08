<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Interfaces\SlackRepositoryInterface;

class SlackRepository implements SlackRepositoryInterface
{
    public function getRooms($slack, $type)
    {
        $rooms = [];
        if ($type == 'all') {
            $channels = $slack->conversationsList()->getChannels();
            $users = $slack->usersList()->getMembers();
            foreach ($channels as $channel) {
                $room = [
                    'name' => $channel->getNameNormalized(),
                    'room_id' => $channel->getId(),
                ];
                array_push($rooms, $room);
            }

            foreach ($users as $user) {
                if (!$user->getIsBot()) {
                    $room = [
                        'name' => $user->getRealName(),
                        'room_id' => $user->getId(),
                    ];
                    array_push($rooms, $room);
                }
            }

            return $rooms;
        } elseif ($type == 'group') {
            $channels = $slack->conversationsList()->getChannels();
            foreach ($channels as $channel) {
                $room = [
                    'name' => $channel->getNameNormalized(),
                    'room_id' => $channel->getId(),
                ];
                array_push($rooms, $room);
            }

            return $rooms;
        } else {
            $users = $slack->usersList()->getMembers();
            foreach ($users as $user) {
                if (!$user->getIsBot()) {
                    $room = [
                        'name' => $user->getRealName(),
                        'room_id' => $user->getId(),
                    ];
                    array_push($rooms, $room);
                }
            }

            return $rooms;
        }
    }
}
