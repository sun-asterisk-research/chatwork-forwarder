<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Payload;
use App\Models\Webhook;
use App\Models\User;
use App\Repositories\Eloquents\PayloadRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayloadRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * get model
     *
     * @return void
     */
    public function testGetModel()
    {
        $botRepository = new PayloadRepository;

        $data = $botRepository->getModel();
        $this->assertEquals(Payload::class, $data);
    }

    /**
     * test create bot
     *
     * @return void
     */
    public function testCreatePayload()
    {
        $botRepository = new PayloadRepository;
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $attributes = [
            'content' => 'sample content',
            'webhook_id' => $webhook->id,
        ];

        $botRepository->create($attributes);

        $this->assertDatabaseHas('payloads', ['content' => 'sample content', 'webhook_id' => $webhook->id]);
    }

    public function testUpdatePayload()
    {
        $payloadRepository = new PayloadRepository;
        $payload = factory(Payload::class)->create(['content' => 'old content']);
        $attributes = [
            'content' => 'new content'
        ];

        $payloadRepository->update($payload->id, $attributes);

        $this->assertDatabaseHas('payloads', ['content' => 'new content', 'id' => $payload->id]);
    }
}
