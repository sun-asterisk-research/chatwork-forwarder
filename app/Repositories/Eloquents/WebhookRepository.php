<?php
namespace App\Repositories\Eloquents;
 
use App\Models\Webhook;
use App\Repositories\Eloquents\BaseRepository;
use Auth;

class WebhookRepository extends BaseRepository
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
}
