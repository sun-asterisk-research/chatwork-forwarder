<?php

namespace Tests\Feature;

use Mockery;
use Auth;
use App\Models\Bot;
use App\Models\User;
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

    /**
     * test user can change status webhook
     *
     * @return  void
     */
    public function testAuthorizedUserCanChangeStatusWebhook()
    {
        $webhook = factory(Webhook::class)->create();
        $this->actingAs($webhook->user);

        $response = $this->put('webhooks/change_status', ['id' => $webhook->id, 'status' => "DISABLED"]);

        $this->assertDatabaseHas('webhooks', ['id' => $webhook->id, 'status' => 0]);
        $response->assertSee('This webhook was updated successfully');
    }

    /**
     * test user change status webhook fail with incorrect id
     *
     * @return  void
     */
    public function testChangeStatusWebhookFailFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $this->actingAs($webhook->user);

        $response = $this->put('webhooks/change_status', ['id' => -1, 'status' => "ENABLED"]);

        $response->assertSee('Updated failed. Something went wrong');
    }

    /**
     * test unauthorized user cannot change status webhook
     *
     * @return  void
     */
    public function testUnauthorizedUserCannotChangeStatusWebhook()
    {
        $webhook = factory(Webhook::class)->create();

        $response = $this->put('webhooks/change_status', ['id' => $webhook->id, 'status' => "ENABLED"]);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUserCanSeeWebhook()
    {
        $webhook = factory(Webhook::class)->create();
        $this->actingAs($webhook->user);

        $response = $this->get(route('webhooks.edit', ['webhook' => $webhook]));
        $response->assertStatus(200);
        $response->assertSee('webhook');
        $response->assertSee('bots');
        $response->assertViewHas('webhookStatuses');
        $response->assertViewHas('payloads');
    }

    public function testUnauthenticateUserCannotSeeWebhook()
    {
        $webhook = factory(Webhook::class)->create();
        $response = $this->get(route('webhooks.edit', ['webhook' => $webhook]));
        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUnauthorizationUserCannotSeeWebhook()
    {
        $webhook = factory(Webhook::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get(route('webhooks.edit', ['webhook' => $webhook]));
        $response->assertStatus(403);
    }

    /**
    * test Feature remove webhook successfully.
    *
    * @return void
    */
   public function testRemoveWebhookFeature()
   {
       $user = factory(User::class)->create();
       $webhook = factory(Webhook::class)->create(['user_id' => $user->id, 'name' => 'test remove webhook success']);

       $this->actingAs($user);
       $response = $this->delete(route('webhooks.destroy', ['webhook' => $webhook]));
       $this->assertDatabaseMissing('webhooks', ['id' => $webhook->id, 'name' => 'test remove webhook success', 'deleted_at' => NULL]);
       $response->assertRedirect(route('webhooks.index'));
       $response->assertStatus(302);
   }

   /**
    * test Feature remove webhook fail.
    *
    * @return void
    */
   public function testRemoveWebhookFailFeature()
   {
       $user = factory(User::class)->create();
       $webhook = factory(Webhook::class)->create(['user_id' => $user->id, 'name' => 'test remove webhook fail']);

       $this->actingAs($user);
       $response = $this->delete(route('webhooks.destroy', ['webhook_id' => ($webhook->id + 99)]));
       $this->assertDatabaseHas('webhooks', ['name' => 'test remove webhook fail']);
       $response->assertStatus(404);
   }

   /**
    * test Feature remove webhook unauthorized
    *
    * @return void
    */
   public function testRemoveWebhookUnauthorizedFeature()
   {
       $user = factory(User::class)->create();
       $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
       $response = $this->delete(route('webhooks.destroy', ['webhook' => $webhook]));

       $response->assertLocation('/login');
       $response->assertStatus(302);
   }

    public function testUserCanUpdateBot()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(
            [
                'name' => 'Created webhook',
                'user_id' => $user->id,
                'description' => 'description create',
                'bot_id' => 1,
                'room_name' => 'Name create',
                'room_id' => 1
            ]);
        $params = [
            'name' => 'Update webhook',
            'description' => 'description Update',
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
            'id' => $webhook->id,
        ];
        
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response->assertRedirect('webhooks/' . $webhook->id . '/edit');
        $this->assertDatabaseHas('webhooks',
            [
                'id' => $webhook->id,
                'name' => 'Update webhook',
                'description' => 'description Update',
                'bot_id' => 1,
                'room_name' => 'Name Update',
                'room_id' => 1,
                'status' => 1,
            ]);
    }
    public function testUpdateWebhookRequireName()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(
            [
                'name' => 'Created webhook',
                'user_id' => $user->id,
                'description' => 'description create',
                'bot_id' => 1,
                'room_name' => 'Name create',
                'room_id' => 1
            ]);
        $params = [
            'name' => NULL,
            'description' => 'description Update',
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
            'id' => $webhook->id,
        ];
        
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response->assertStatus(302)->assertSessionHasErrors('name');
    }
}
