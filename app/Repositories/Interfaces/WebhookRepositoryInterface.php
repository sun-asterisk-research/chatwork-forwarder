<?php

namespace App\Repositories\Interfaces;

interface WebhookRepositoryInterface
{
    /**
     * Get all webhooks by user
     * @return mixed
     */
    public function getAllByUser($perPage);

    /**
     * Get all webhooks by admin
     * @return mixed
     */
    public function getAllAndSearch($perPage, $searchParams);

    /**
     * Get all webhooks by user
     * @return mixed
     */
    public function getAllByUserForDropdown();
}
