<?php

namespace Tests\Feature\Models;

use App\Models\Mapping;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MappingTest extends TestCase
{
    use RefreshDatabase;

    public function test_contains_valid_fillable_properties()
    {
        $fillable = [
            'webhook_id', 'name', 'key', 'value'
        ];
        $model = new Mapping();

        $this->assertEquals($fillable, $model->getFillable());
    }

    public function test_webhook_relation()
    {
        $model = new Mapping();
        $relation = $model->webhook();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    /**
     * test scope get mapping by key
     *
     * @return void
     */
    public function testScopeByKey()
    {
        factory(Mapping::class, 2)->create(['key' => 'qtv']);
        factory(Mapping::class)->create(['key' => 'not me']);

        $result = Mapping::byKey('qtv');

        $this->assertEquals(2, $result->count());
    }
}
