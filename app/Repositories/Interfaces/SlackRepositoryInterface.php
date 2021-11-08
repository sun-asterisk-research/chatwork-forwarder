<?php

namespace App\Repositories\Interfaces;

interface SlackRepositoryInterface
{
    /**
     * Get rooms by bot key
     * @return mixed
     */
    public function getRooms($slack, $type);
}
