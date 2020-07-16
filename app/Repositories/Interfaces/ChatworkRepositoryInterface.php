<?php

namespace App\Repositories\Interfaces;

interface ChatworkRepositoryInterface
{
    /**
     * Get rooms by bot key
     * @return mixed
     */
    public function getRooms($chatwork, $type);
}
