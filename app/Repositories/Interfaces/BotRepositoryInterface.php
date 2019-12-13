<?php

namespace App\Repositories\Interfaces;

interface BotRepositoryInterface
{
    /**
     * Get all bot by user
     * @return mixed
     */
    public function getAllByUser($perPage);
}
