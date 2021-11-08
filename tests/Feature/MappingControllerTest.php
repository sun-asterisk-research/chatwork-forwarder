<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Webhook;
use App\Models\Mapping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Enums\UserType;
use Mockery;
use Exception;
use Illuminate\Database\QueryException;
use App\Repositories\Interfaces\MappingRepositoryInterface as MappingRepository;

class MappingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature delete mapping successfully.
     *
     * @return void
     */
    public function testRemoveMappingFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id]);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.mappings.destroy', ['webhook' => $webhook, 'mapping' => $mapping]));
        $this->assertDatabaseMissing('mappings', ['id' => $mapping->id, 'deleted_at' => null]);
        $response->assertRedirect(route('webhooks.edit', $webhook));
        $response->assertStatus(302);
    }

    /**
     * test Feature delete mapping raise exception
     *
     * @return void
     */
     public function testDeleteMappingFailedRaiseException()
     {
         $mapping = factory(Mapping::class)->create();
         $user = $mapping->webhook->user;
         $data = [
             'name' => 'Tran Van A',
             'key' => 'tranvana',
             'value' => '[To:123123] Tran Van A',
         ];

         $this->actingAs($user);

         $mock = Mockery::mock(MappingRepository::class);
         $mock->shouldReceive('delete')->andThrowExceptions([new Exception('Exception', 100)]);
         $this->app->instance(MappingRepository::class, $mock);

         $response = $this->delete(route('webhooks.mappings.destroy', ['webhook' => $mapping->webhook, 'mapping' => $mapping]));

         $response->assertSessionHas('messageFail', [
             'status' => 'Delete failed',
             'message' => 'Delete failed. Something went wrong',
         ]);
     }

    /**
     * test Feature delete mapping not exists.
     *
     * @return void
     */
    public function testRemoveMappingFailFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id]);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.mappings.destroy', ['webhook' => $webhook, 'mapping' => -1]));
        $this->assertDatabaseHas('mappings', ['id' => $mapping->id]);
        $response->assertStatus(404);
    }

    /**
     * test Feature delete mapping unauthenticated
     *
     * @return void
     */
    public function testRemoveMappingUnauthenticatedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id]);
        $response = $this->delete(route('webhooks.mappings.destroy', ['webhook' => $webhook, 'mapping' => $mapping]));

        $response->assertLocation('/');
        $response->assertStatus(302);
    }

    /**
     * test Feature delete Permision denied. Mapping belong to another user
     *
     * @return void
     */
    public function testRemoveMappingFailPermissionDenied1Feature()
    {
        $user = factory(User::class)->create();
        $another_user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $another_user->id]);
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id]);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.mappings.destroy', ['webhook' => $webhook, 'mapping' => $mapping]));
        $this->assertDatabaseHas('mappings', ['id' => $mapping->id]);
        $response->assertStatus(403);
    }

    /**
     * test Feature delete Permision denied. Mapping belong to another webhook
     *
     * @return void
     */
    public function testRemoveMappingFailPermissionDenied2Feature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $another_webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id]);
        $this->actingAs($user);

        $response = $this->delete(route('webhooks.mappings.destroy', ['webhook' => $another_webhook, 'mapping' => $mapping]));
        $this->assertDatabaseHas('mappings', ['id' => $mapping->id]);
        $response->assertStatus(403);
    }

    /**
     * test User can see create mapping form
     *
     * @return void
     */
    public function testUserCanSeeCreateMappingForm()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;

        $this->actingAs($user);
        $response = $this->get(route('webhooks.mappings.create', $webhook));

        $response->assertStatus(200);
        $response->assertViewHas('webhook');
    }

    /**
     * test Admin can't see create mapping form
     *
     * @return void
     */
    // public function testUserCantSeeCreateMappingForm()
    // {
    //     $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
    //     $webhook = factory(Webhook::class)->create();
    //     $user = $webhook->user;

    //     $this->actingAs($admin);
    //     $response = $this->get(route('webhooks.mappings.create', $webhook));

    //     $response->assertStatus(403);
    // }

    /**
     * test Feature create mapping successfully
     *
     * @return void
     */
    public function testCreateMappingSuccessFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'keys' => ['tranvana'],
            'values' => ['[To:123123] Tran Van A'],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook->id), $data);

        $response->assertStatus(200)
            ->assertSessionHas('messageSuccess');
            $response->assertSessionHas('messageSuccess', [
                'status' => 'Create success',
                'message' => 'This mapping successfully created',
            ]);
    }

    /**
     * test Feature create mapping failed, key too long than 100 characters
     *
     * @return void
     */
    public function testCreateMappingFailedKeyTooLongFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'keys' => [Str::random(1010)],
            'values' => ['[To:123123] Tran Van A'],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('keys.0');
    }

    /**
     * test Feature create mapping failed, key is required
     *
     * @return void
     */
    public function testCreateMappingFailedKeyRequiredFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'keys' => [''],
            'values' => ['[To:123123] Tran Van A'],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('keys.0');
    }

    /**
     * test Feature create mapping failed, value is required
     *
     * @return void
     */
    public function testCreateMappingFailedValueRequireFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'keys' => ['tranvana'],
            'values' => [''],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('values.0');
    }

    /**
     * test Feature create mapping failed, value too long than 100 characters
     *
     * @return void
     */
    public function testCreateMappingFailedValueTooLongFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'keys' => ['tranvana'],
            'values' => [Str::random(101)],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('values.0');
    }

    /**
     * test feature create mapping failed, user is unauthentication
     *
     * @return void
     */
    public function testCreateMappingFailedUnauthenticationFeature()
    {
        $webhook = factory(Webhook::class)->create();

        $data = [
            'keys' => ['tranvana'],
            'values' => [Str::random(101)],
            'webhook_id' => $webhook->id,
        ];

        $response = $this->post(route('webhooks.mappings.store', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertRedirect('/');
    }


    /**
     * test feature create mapping failed, webhook not belongs to current user
     *
     * @return void
     */
    public function testCreateMappingFailedWebhookNotBelongToUserFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = factory(User::class)->create();
        $data = [
            'keys' => ['tranvana'],
            'values' => [Str::random(101)],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook->id), $data);

        $response->assertStatus(302);
    }

    /**
     * test User can see edit mapping form
     *
     * @return void
     */
    public function testUserCanSeeEditMappingForm()
    {
        $mapping = factory(Mapping::class)->create();
        $user = $mapping->webhook->user;

        $this->actingAs($user);
        $response = $this->get(route('webhooks.mappings.edit', ['webhook' => $mapping->webhook, 'mapping' => $mapping]));

        $response->assertStatus(200);
        $response->assertViewHas(['webhook', 'mappings']);
    }

    /**
     * test Feature update mapping successfully
     *
     * @return void
     */
    public function testUpdateMappingSuccessFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id]);

        $data = [
            'keys' => ['tranvana'],
            'values' => ['[To:123123] Tran Van A'],
            'webhook_id' => $webhook->id,
        ];
        $this->actingAs($user);
        $response = $this->post(route('webhooks.update.mappings', $webhook->id), $data);

        $response->assertStatus(200)
            ->assertSessionHas('messageSuccess');
        $response->assertSessionHas('messageSuccess', [
            'status' => 'Update success',
            'message' => 'This mapping successfully updated',
        ]);
    }

    /**
     * test Feature update mapping failed, key too long than 100 characters
     *
     * @return void
     */
    public function testUpdateMappingFailedKeyTooLongFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'keys' => [Str::random(1010)],
            'values' => ['[To:123123] Tran Van A'],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.update.mappings', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('keys.0');
    }

    /**
     * test Feature update mapping failed, key is required
     *
     * @return void
     */
    public function testUpdateMappingFailedKeyRequiredFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'keys' => [''],
            'values' => ['[To:123123] Tran Van A'],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.update.mappings', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('keys.0');
    }

    /**
     * test Feature update mapping failed, value is required
     *
     * @return void
     */
    public function testUpdateMappingFailedValueRequireFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'keys' => ['hihi'],
            'values' => [''],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.update.mappings', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('values.0');
    }

    /**
     * test Feature update mapping failed, value too long than 100 characters
     *
     * @return void
     */
    public function testUpdateMappingFailedValueTooLongFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'values' => [Str::random(101)],
            'keys' => ['[To:123123] Tran Van A'],
            'webhook_id' => $webhook->id,
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.update.mappings', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('values.0');
    }

    /**
     * test feature update mapping failed, user is unauthentication
     *
     * @return void
     */
    public function testUpdateMappingFailedUnauthenticationFeature()
    {
        $webhook = factory(Webhook::class)->create();

        $data = [
            'values' => [Str::random(101)],
            'keys' => ['[To:123123] Tran Van A'],
            'webhook_id' => $webhook->id,
        ];

        $response = $this->post(route('webhooks.update.mappings', $webhook->id), $data);

        $response->assertStatus(302)
            ->assertRedirect('/');
    }
}
