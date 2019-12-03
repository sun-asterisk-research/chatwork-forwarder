<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Webhook;
use App\Models\Payload;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayloadControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature remove payload successfully.
     *
     * @return void
     */
    public function testRemovePayloadFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'test remove payload']);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload' => $payload]));
        $this->assertDatabaseMissing('payloads', ['id' => $payload->id, 'content' => 'test remove payload']);
        $response->assertRedirect(route('webhooks.edit', $webhook));
        $response->assertStatus(302);
    }

    /**
     * test Feature remove payload fail.
     *
     * @return void
     */
    public function testRemovePayloadFailFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'test remove payload fail']);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload_id' => ($payload->id + 99)]));
        $this->assertDatabaseHas('payloads', ['content' => 'test remove payload fail']);
        $response->assertStatus(404);
    }

    /**
     * test Feature remove payload unauthorized
     *
     * @return void
     */
    public function testRemovePayloadUnauthorizedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'test remove payload fail']);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload_id' => 1]));

        $response->assertLocation('/login');
        $response->assertStatus(302);
    }
    
    /**
    * test Feature remove Permision denied. Payload belong to another user
    *
    * @return void
    */
    public function testRemovePayloadFailPermissionDenied1Feature()
    {
        $user = factory(User::class)->create();
        $another_user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $another_user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'payload of another user']);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload_id' => ($payload->id)]));
        $this->assertDatabaseHas('payloads', ['content' => 'payload of another user']);
        $response->assertStatus(403);
    }

    /**
     * test Feature remove Permision denied. Payload belong to another webhook
     *
     * @return void
    */
    public function testRemovePayloadFailPermissionDenied2Feature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $another_webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $another_payload = factory(Payload::class)->create(['webhook_id' => $another_webhook->id, 'content' => 'payload of another webhook']);
        $this->actingAs($user);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload_id' => ($another_payload->id)]));
        $this->assertDatabaseHas('payloads', ['content' => 'payload of another webhook']);
        $response->assertStatus(403);
    }

    /**
     * test Feature show create payload view successfully
     *
     * @return void
     */
    public function testShowCreateViewPayloadFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get(route('webhooks.payloads.create', $webhook));
        $response->assertStatus(200);
        $response->assertViewHas('webhook');
    }

    /**
     * test Feature show create payload view when user not authorized
     *
     * @return void
     */
    public function testShowCreateViewPayloadUnauthorizedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);

        $response = $this->get(route('webhooks.payloads.create', $webhook));
        $response->assertLocation('/login');
        $response->assertStatus(302);
    }

    /**
     * test Feature show create payload view when webhook not exist
     *
     * @return void
     */
    public function testShowCreateViewPayloadOfWebhookNotExistFeature()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get(route('webhooks.payloads.create', -1));
        $response->assertStatus(404);
    }

    /**
     * test Feature store payload successfully
     *
     * @return void
     */
    public function testStorePayloadSuccessFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->post(route('webhooks.payloads.store', $webhook), [
            'content' => 'sample content',
            'fields' => ['$payload->name', '$payload->age'],
            'operators' => ['==', '>'],
            'values' => ['rammus', '30']
        ]);
        $response->assertStatus(200);
        $response->assertSessionHas('messageSuccess', 'This payload successfully created');
    }

    /**
     * test Feature store payload violate validation rule
     *
     * @return void
     */
    public function testStorePayloadFailedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->post(route('webhooks.payloads.store', $webhook), [
            'content' => '',
            'fields' => ['$payload->name', '$payload->age'],
            'operators' => ['==', '>'],
            'values' => ['rammus', '30']
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'content' => 'Please enter content'
        ]);
    }

    /**
     * test Feature store payload with not existed webhook
     *
     * @return void
     */
    public function testStorePayloadOfWebhookNotExistFeature()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->post(route('webhooks.payloads.store', -1), [
            'content' => 'sample content',
            'fields' => ['$payload->name', '$payload->age'],
            'operators' => ['==', '>'],
            'values' => ['rammus', '30']
        ]);
        $response->assertStatus(404);
    }
}
