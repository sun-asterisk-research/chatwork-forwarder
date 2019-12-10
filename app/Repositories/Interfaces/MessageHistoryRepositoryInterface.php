<?php

namespace App\Repositories\Interfaces;

interface MessageHistoryRepositoryInterface
{
    /**
     * list message history belongs to Payload History
     *
     * @param int $id
     * @param string $keyword
     * @return void
     */
    public function getAllAndSearch($id, $keyword);
}
