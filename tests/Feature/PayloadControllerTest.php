<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Webhook;
use App\Models\Payload;
use Illuminate\Foundation\Testing\WithFaker;
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
}
