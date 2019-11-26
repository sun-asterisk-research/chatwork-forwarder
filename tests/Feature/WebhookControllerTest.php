<?php

namespace Tests\Feature;

use Mockery;
use Auth;
use App\Models\Bot;
use Tests\TestCase;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature list webhooks.
     *
     * @return void
     */
    public function testShowListWebhookFeature()
    {
        $webhookLists = factory(Webhook::class, 2)->create();
        $user = $webhookLists[0]->user;

        $this->actingAs($user);
        $response = $this->get('/webhooks');
        $response->assertStatus(200);
        $response->assertViewHas('webhooks');
    }

    /**
     * test Feature show create view webhook.
     *
     * @return void
     */
    public function testShowCreateViewWebhookFeature()
    {
        $webhookLists = factory(Webhook::class, 2)->create();
        factory(Bot::class, 2)->create();
        $user = $webhookLists[0]->user;

        $this->actingAs($user);
        $response = $this->get('/webhooks/create');
        $response->assertStatus(200);
        $response->assertViewHas('webhookStatuses');
        $response->assertViewHas('bots');
    }

    /**
     * test Feature store data webhook successfully
     *
     * @return void
     */
    public function testStoreWebhookSuccessFeature()
    {
        $webhookLists = factory(Webhook::class, 2)->create();
        factory(Bot::class, 2)->create();
        $user = $webhookLists[0]->user;

        $this->actingAs($user);
        $response = $this->post(route('webhooks.store'), ['name' => 'string', 'description' => 'some thing successfully', 'bot_id' => 1, 'room_name' => 'string', 'room_id' => 1]);

        $response->assertRedirect();
    }

    /**
     * test Feature store data webhook fail
     *
     * @return void
     */
    public function testStoreWebhookFailFeature()
    {
        $this->withoutMiddleware();
        $response = $this->post(route('webhooks.store'), ['name' => 'string', 'description' => 'some thing successfully', 'bot_id' => 1, 'room_name' => 'string', 'room_id' => 1]);

        $response->assertRedirect();
        $response->assertStatus(302);
    }
}
