<?php

namespace App\Repositories\Eloquents;

use Auth;
use App\Models\Webhook;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\WebhookRepositoryInterface;

class WebhookRepository extends BaseRepository implements WebhookRepositoryInterface
{
    public function getModel()
    {
        return Webhook::class;
    }

    public function getAllByUser($perPage)
    {
        return Auth::user()->webhooks()
                            ->orderBy('webhooks.status', 'desc')
                            ->orderBy('webhooks.created_at', 'desc')
                            ->paginate($perPage);
    }

    public function getAllByUserForDropdown()
    {
        return Auth::user()->webhooks()
                            ->orderBy('webhooks.name')
                            ->get();
    }

    public function getAllAndSearch($perPage, $searchParams)
    {
        $query = $this->model->with('user')
                        ->orderBy('webhooks.status', 'desc')
                        ->orderBy('webhooks.created_at', 'desc');
        if ($searchParams) {
            $searchParams = $this->handleSearchParams(['name', 'status'], $searchParams);

            return $query->search($searchParams, $perPage);
        } else {
            return $query->paginate($perPage);
        }
    }
}
