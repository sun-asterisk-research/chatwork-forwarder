<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Condition;

class ConditionTest extends TestCase
{
    /**
     * test contains valid fillable properties
     *
     * @return void
     */
    public function testContainsValidFillableProperties()
    {
        $fillable = [
            'payload_id',
            'field',
            'operator',
            'value',
        ];

        $model = new Condition();
        $this->assertEquals($fillable, $model->getFillable());
    }

    /**
     * test using softDeletes
     *
     * @return void
     */
    public function testUsingSoftDeleted()
    {
        $condition = new condition();

        $this->assertContains('deleted_at', $condition->getDates());
    }
}
