<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Payload;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayloadTest extends TestCase
{
    /**
     * test contains valid fillable properties
     *
     * @return void
     */
    public function testContainsValidFillableProperties()
    {
        $fillable = [
            'webhook_id',
            'content',
            'params',
        ];

        $model = new Payload();

        $this->assertEquals($fillable, $model->getFillable());
    }

    /**
     * test using softDeletes
     *
     * @return void
     */
    public function testUsingSoftDeleted()
    {
        $payload = new Payload();

        $this->assertContains('deleted_at', $payload->getDates());
    }

    /**
     * test webhook related
     *
     * @return void
     */
    public function testWebhookRelate()
    {
        $payload = new Payload();

        $this->assertInstanceOf(BelongsTo::class, $payload->webhook());
    }

    /**
     * test condition related
     *
     * @return void
     */
    public function testConditionRelate()
    {
        $payload = new Payload();

        $this->assertInstanceOf(HasMany::class, $payload->conditions());
    }
}
