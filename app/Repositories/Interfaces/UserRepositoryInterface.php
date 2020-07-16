<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    /**
     * Get all webhooks by user
     * @return mixed
     */
    public function store($data);

    public function getAllAndSearch($perPage, $keyword);
}
