<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Mapping;
use App\Models\Webhook;
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

        $this->assertDatabaseMissing('mappings', ['id' => $mapping->id, 'deleted_at' => null]);
    }

    public function testGetKeys()
    {
        $mappingRepository = new MappingRepository;
        $webhook = factory(Webhook::class)->create();
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id, 'key' => 'my key', 'value' => 'this value']);
        $keys = $mappingRepository->getKeys($webhook);

        $this->assertEquals('my key', $keys[0]);
    }

    public function testGetKeyAndValues()
    {
        $mappingRepository = new MappingRepository;
        $webhook = factory(Webhook::class)->create();
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id, 'key' => 'my key', 'value' => 'this value']);
        $result = $mappingRepository->getKeyAndValues($webhook);

        $this->assertEquals(collect([
            'my key' => 'this value',
        ]), $result);
    }
}
