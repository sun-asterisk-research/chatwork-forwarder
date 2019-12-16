<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Mapping;
use App\Models\User;
use App\Repositories\Eloquents\MappingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MappingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * get model
     *
     * @return void
     */
    public function testGetModel()
    {
        $mappingRepository = new MappingRepository;

        $data = $mappingRepository->getModel();
        $this->assertEquals(Mapping::class, $data);
    }

    /**
     * test update mapping
     *
     * @return void
     */
    public function testDestroyMapping()
    {
        $mappingRepository = new MappingRepository;
        $mapping = factory(Mapping::class)->create();

        $mappingRepository->delete($mapping->id);

        $this->assertDatabaseMissing('mappings', ['id' => $mapping->id, 'deleted_at' => NULL]);
    }
}
