<?php

namespace App\Repositories\Interfaces;

interface WebhookRepositoryInterface
{
    /**
     * Get all webhooks by user
     * @return mixed
     */
    public function getAllByUser();
}
