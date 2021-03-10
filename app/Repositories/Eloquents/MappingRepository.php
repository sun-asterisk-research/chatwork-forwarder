<?php
namespace App\Repositories\Eloquents;

use App\Models\Mapping;
use App\Models\Webhook;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\MappingRepositoryInterface;

class MappingRepository extends BaseRepository implements MappingRepositoryInterface
{
    public function getModel()
    {
        return Mapping::class;
    }

    public function getKeys(Webhook $webhook)
    {
        return Mapping::where('webhook_id', $webhook->id)->pluck('key')->toArray();
    }

    public function getKeyAndValues(Webhook $webhook)
    {
        return Mapping::select('key', 'value')->where('webhook_id', $webhook->id)
            ->get()
            ->pluck('value', 'key');
    }
}
