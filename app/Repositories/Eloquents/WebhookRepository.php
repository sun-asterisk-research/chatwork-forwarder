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

    public function getAllByUser()
    {
        return Auth::user()->webhooks()
                            ->orderBy('webhooks.status', 'desc')
                            ->orderBy('webhooks.created_at', 'desc')
                            ->get();
    }

    public function getAll()
    {
        return $this->model->with('user')
                        ->orderBy('webhooks.status', 'desc')
                        ->orderBy('webhooks.created_at', 'desc')
                        ->get();
    }
}
