<?php

namespace App\Repositories\Eloquents;

use App\Models\Mapping;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\MappingRepositoryInterface;

class MappingRepository extends BaseRepository implements MappingRepositoryInterface
{
    public function getModel()
    {
        return Mapping::class;
    }
}
