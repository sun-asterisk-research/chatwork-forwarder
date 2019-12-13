<?php

namespace Tests\Feature\Models;

use App\Models\Mapping;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MappingTest extends TestCase
{
    public function test_contains_valid_fillable_properties()
    {
        $fillable = [
            'webhook_id', 'name', 'key', 'value'
        ];
        $model = new Mapping();

        $this->assertEquals($fillable, $model->getFillable());
    }

    public function test_webhook_relation(){
        $model = new Mapping();
        $relation = $model->webhook();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }
}
