<?php

namespace App\Repositories\Interfaces;

interface PayloadHistoryRepositoryInterface
{
    public function getAllAndSearch($perPage, $searchParams);
}
