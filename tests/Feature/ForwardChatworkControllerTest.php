<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Payload;

class ForwardChatworkControllerTest extends TestCase
{
    /**
     * test forward message success
     *
     * @return void
     */
    public function testForwardMessageSuccess()
    {
        $webhook = factory(Webhook::class)->create();
        $params = [
            'status' => 'Active',
            'name' => 'qtv'
        ];

        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);

        $response = $this->post('api/v1/webhooks/' . $webhook->token, $params);

        $response->assertStatus(200)
            ->assertSeeText("Excuted successfully");

        $this->assertDatabaseHas('payload_histories', [
            'webhook_id' => $webhook->id,
            'status' => 0, 'log' => ''
        ]);
    }

    /**
     * test forward message faild - webhook not have payload or payload not matching with condition
     *
     * @return void
     */
    public function testForwardMessageFailedWithNotHavePayload()
    {
        $webhook = factory(Webhook::class)->create();
        $params = [
            'status' => 'Active',
            'name' => 'qtv'
        ];
        $response = $this->post('api/v1/webhooks/' . $webhook->token, $params);

        $response->assertStatus(200)
            ->assertSeeText("Excuted successfully");

        $this->assertDatabaseHas('payload_histories', [
            'webhook_id' => $webhook->id,
            'status' => 1,
            'log' => 'Not found payload.'
        ]);
    }

    /**
     * test forward message faild - webhook not found
     *
     * @return void
     */
    public function testNotFoundWebhook()
    {
        $params = [
            'status' => 'Active',
            'name' => 'qtv'
        ];

        $response = $this->post('api/v1/webhooks/23424234234234234', $params);
        $response->assertStatus(404)
            ->assertSeeText("Webhook not found. Please try again");
    }
}
