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
use App\Enums\UserType;
use Illuminate\Support\Str;

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

        $response->assertStatus(403);
    }

    /**
     * test user change status webhook fail with permission denied
     *
     * @return  void
     */
    public function testChangeStatusWebhookFailWithPermissionDeniedFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->put('webhooks/change_status', ['id' => $webhook->id, 'status' => "ENABLED"]);
        $response->assertStatus(403);
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

    /**
     * test admin can change status of user's webhook
     *
     * @return  void
     */
    public function testAuthorizedAdminCanChangeStatusWebhookOfUser()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($admin);

        $response = $this->put('webhooks/change_status', ['id' => $webhook->id, 'status' => "DISABLED"]);

        $this->assertDatabaseHas('webhooks', ['id' => $webhook->id, 'status' => 0]);
        $response->assertSee('This webhook was updated successfully');
    }

    /**
     * test admin can change status of admin's webhook
     *
     * @return  void
     */
    public function testAuthorizedAdminCanChangeStatusWebhookOfAdmin()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $webhook = factory(Webhook::class)->create(['user_id' => $admin->id]);

        $this->actingAs($admin);

        $response = $this->put('webhooks/change_status', ['id' => $webhook->id, 'status' => "DISABLED"]);

        $this->assertDatabaseHas('webhooks', ['id' => $webhook->id, 'status' => 0]);
        $response->assertSee('This webhook was updated successfully');
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

    /**
     * test update webhook suscess
     * 
     * @return void
     */
    public function testUpdateWebhookFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook',
            'user_id' => $user->id,
            'description' => 'description create',
            'bot_id' => 1,
            'room_name' => 'Name create',
            'room_id' => 1
        ]);
        $params = [
            'name' => "Name update",
            'description' => 'description Update',
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response->assertRedirect('webhooks/' . $webhook->id . '/edit');
        $this->assertDatabaseHas('webhooks',
            [
                'id' => $webhook->id,
                'name' => 'Name update',
                'description' => 'description Update',
                'bot_id' => '1',
                'room_name' => 'Name Update',
                'room_id' => 1, 'status' => 1,
            ]);
    }

    /**
     * test unauthenticate user cannot update webhook
     *
     * @return  void
     */
    public function testUnauthenticateUserCannotUpdateWebhook()
    {
        $webhook = factory(Webhook::class)->create();
        $params = [
            'name' => "Name update",
            'description' => 'description Update',
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];

        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    
    /**
     * test unauthorization user cannot update webhook
     *
     * @return  void
     */
    public function testUnauthorizationUserCannotUpdateWebhook()
    {
        $webhook = factory(Webhook::class)->create();
        $user = factory(User::class)->create();
        $params = [
            'name' => "Name update",
            'description' => 'description Update',
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response->assertStatus(403);
    }

    /**
     * test Webhook required name
     * 
     * @return void
     */
    public function testUpdateWebhookRequireName()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook',
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
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');

    }

    /**
     * test update webhook unique name with a user
     * 
     * @return void
     */
    public function testUpdateWebhookUniqueNameWithUser()
    {
        $user = factory(User::class)->create();
        $webhook_1 = factory(Webhook::class)->create(['name' => 'Created webhook 1', 'user_id' => $user->id]);
        $webhook_2 = factory(Webhook::class)->create(['name' => 'Created webhook 2', 'user_id' => $user->id]);
        
        $params = [
            'name' => $webhook_1->name,
            'description' => 'description Update',
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];
        
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook_2->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test wenhook name have maximum length is 50 characters
     * 
     * @return void
     */
    public function testUpdateWebhookNameMaximumLength()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook', 'user_id' => $user->id]);
        $params = [
            'name' => '1234567891234567891234567891234567891234567891234567891234567',
            'description' => 'description Update',
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];
        
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test Webhook required description 
     * 
     * @return void
     */
    public function testUpdateWebhookRequireDescription()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook',
            'user_id' => $user->id,
            'description' => 'description create',
            'bot_id' => 1,
            'room_name' => 'Name create',
            'room_id' => 1
        ]);
        $params = [
            'name' => "Name update",
            'description' => "DCDF",
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('description');
    }

    /**
     * test wenhook name have min length is 10 characters
     * 
     * @return void
     */
    public function testUpdateWebhookDescriptionMinLength()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook',
            'user_id' => $user->id,
            'description' => 'des',
            'bot_id' => 1,
            'room_name' => 'Name create',
            'room_id' => 1
        ]);
        $params = [
            'name' => "Name update",
            'description' => "DCDF",
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('description');
    }

    /**
     * test webhook description have maximum length is 1000 characters
     * 
     * @return void
     */
    public function testUpdateWebhookDescriptionMaximumLength()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook',
            'user_id' => $user->id,
            'description' => '1234567891234567891234567 891234567891234567890',
            'bot_id' => 1,
            'room_name' => 'Name create',
            'room_id' => 1
        ]);
        $params = [
            'name' => "Name update",
            'description' => Str::random(1001),
            'bot_id' => 1,
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('description');
    }

    /**
     * test Webhook required bot_id 
     * 
     * @return void
     */
    public function testUpdateWebhookRequireBotId()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook',
            'user_id' => $user->id,
            'description' => 'description create',
            'bot_id' => 1,
            'room_name' => 'Name create',
            'room_id' => 1
        ]);
        $params = [
            'name' => "Name update",
            'description' => "description Update",
            'room_name' => 'Name Update',
            'room_id' => 1,
            'status' => 1,
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('bot_id');
    }

    /**
     * test Webhook required room_name 
     * 
     * @return void
     */
    public function testUpdateWebhookRequireRoomName()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook',
            'user_id' => $user->id,
            'description' => 'description create',
            'bot_id' => 1,
            'room_name' => 'Name create',
            'room_id' => 1
        ]);
        $params = [
            'name' => "Name update",
            'description' => "description Update",
            'room_id' => 1,
            'status' => 1,
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('room_name');
    }

    /**
     * test Webhook required room_id 
     * 
     * @return void
     */
    public function testUpdateWebhookRequireRoomId()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['name' => 'Created webhook',
            'user_id' => $user->id,
            'description' => 'description create',
            'bot_id' => 1,
            'room_name' => 'Name create',
            'room_id' => 1
        ]);
        $params = [
            'name' => "Name update",
            'description' => "description Update",
            'room_name' => 'Name Update',
            'status' => 1,
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.update', $webhook->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('room_id');
    }
}
