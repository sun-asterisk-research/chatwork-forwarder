<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Webhook;
use App\Models\Mapping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

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

        $response->assertLocation('/login');
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
     * test Feature create mapping successfully
     *
     * @return void
     */
    public function testCreateMappingSuccessFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'name' => 'Tran Van A',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHas('messageSuccess');
        $this->assertDatabaseHas('mappings', ['name' => 'Tran Van A', 'webhook_id' => $webhook->id]);
    }

    /**
     * test Feature create mapping failed, name too long than 100 characters
     *
     * @return void
     */
    public function testCreateMappingFailedNameTooLongFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'name' => Str::random(101),
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test Feature create mapping failed, name is required
     *
     * @return void
     */
    public function testCreateMappingFailedNameRequiredFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'name' => '',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test Feature create mapping failed, name is duplicate within a webhook
     *
     * @return void
     */
    public function testCreateMappingFailedNameDuplicateFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'name' => 'nguyen van a',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];
        factory(Mapping::class)->create(['name' => 'nguyen van a', 'webhook_id' => $webhook->id]);

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
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
            'name' => 'Tran Van A',
            'key' => Str::random(101),
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('key');
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
            'name' => 'Tran Van A',
            'key' => '',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('key');
    }

    /**
     * test Feature create mapping failed, key is duplicate within a webhook
     *
     * @return void
     */
    public function testCreateMappingFailedKeyDuplicateFeature()
    {
        $webhook = factory(Webhook::class)->create();
        $user = $webhook->user;
        $data = [
            'name' => 'nguyen van a',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];
        factory(Mapping::class)->create(['key' => 'tranvana', 'webhook_id' => $webhook->id]);

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('key');
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
            'name' => 'nguyen van a',
            'key' => 'tranvana',
            'value' => '',
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('value');
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
            'name' => 'Tran Van A',
            'key' => 'tranvana',
            'value' => Str::random(101),
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('value');
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
            'name' => 'Tran Van A',
            'key' => 'tranvana',
            'value' => Str::random(101),
        ];

        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(302)
            ->assertRedirect('login');
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
            'name' => 'Tran Van A',
            'key' => 'tranvana',
            'value' => '123213',
        ];

        $this->actingAs($user);
        $response = $this->post(route('webhooks.mappings.store', $webhook), $data);

        $response->assertStatus(403);
    }

    /**
     * test Feature update mapping successfully
     *
     * @return void
     */
    public function testUpdateMappingSuccessFeature()
    {
        $mapping = factory(Mapping::class)->create();
        $user = $mapping->webhook->user;
        $data = [
            'name' => 'Tran Van A',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHas('messageSuccess');
        $this->assertDatabaseHas('mappings', ['name' => 'Tran Van A', 'webhook_id' => $mapping->webhook->id]);
    }

    /**
     * test Feature update mapping failed, name too long than 100 characters
     *
     * @return void
     */
    public function testUpdateMappingFailedNameTooLongFeature()
    {
        $mapping = factory(Mapping::class)->create();
        $user = $mapping->webhook->user;
        $data = [
            'name' => Str::random(101),
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test Feature update mapping failed, name is required
     *
     * @return void
     */
    public function testUpdateMappingFailedNameRequiredFeature()
    {
        $mapping = factory(Mapping::class)->create();
        $user = $mapping->webhook->user;
        $data = [
            'name' => '',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test Feature update mapping failed, name is duplicate within a webhook
     *
     * @return void
     */
    public function testUpdateMappingFailedNameDuplicateFeature()
    {
        $mapping = factory(Mapping::class)->create(['name' => 'nguyen van a']);
        $user = $mapping->webhook->user;
        $data = [
            'name' => 'nguyen van a',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test Feature update mapping failed, key too long than 100 characters
     *
     * @return void
     */
    public function testUpdateMappingFailedKeyTooLongFeature()
    {
        $mapping = factory(Mapping::class)->create();
        $user = $mapping->webhook->user;
        $data = [
            'name' => 'tran van A',
            'key' => Str::random(101),
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('key');
    }

    /**
     * test Feature update mapping failed, key is required
     *
     * @return void
     */
    public function testUpdateMappingFailedKeyRequiredFeature()
    {
        $mapping = factory(Mapping::class)->create();
        $user = $mapping->webhook->user;
        $data = [
            'name' => 'tran van A',
            'key' => '',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('key');
    }

    /**
     * test Feature update mapping failed, key is duplicate within a webhook
     *
     * @return void
     */
    public function testUpdateMappingFailedKeyDuplicateFeature()
    {
        $mapping = factory(Mapping::class)->create(['key' => 'tranvana']);
        $user = $mapping->webhook->user;
        $data = [
            'name' => 'tran van A',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('key');
    }

    /**
     * test Feature update mapping failed, value is required
     *
     * @return void
     */
    public function testUpdateMappingFailedValueRequireFeature()
    {
        $mapping = factory(Mapping::class)->create();
        $user = $mapping->webhook->user;
        $data = [
            'name' => 'tran van A',
            'key' => 'tranvana',
            'value' => '',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('value');
    }

    /**
     * test Feature update mapping failed, value too long than 100 characters
     *
     * @return void
     */
    public function testUpdateMappingFailedValueTooLongFeature()
    {
        $mapping = factory(Mapping::class)->create();
        $user = $mapping->webhook->user;
        $data = [
            'name' => 'tran van A',
            'key' => 'tranvana',
            'value' => Str::random(101),
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertSessionHasErrors('value');
    }

    /**
     * test feature update mapping failed, user is unauthentication
     *
     * @return void
     */
    public function testUpdateMappingFailedUnauthenticationFeature()
    {
        $mapping = factory(Mapping::class)->create();

        $data = [
            'name' => 'Tran Van A',
            'key' => 'tranvana',
            'value' => '[To:123123] Tran Van A',
        ];

        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(302)
            ->assertRedirect('login');
    }


    /**
     * test feature update mapping failed, webhook not belongs to current user
     *
     * @return void
     */
    public function testUpdateMappingFailedWebhookNotBelongToUserFeature()
    {
        $mapping = factory(Mapping::class)->create();
        $user = factory(User::class)->create();
        $data = [
            'name' => 'Tran Van A',
            'key' => 'tranvana',
            'value' => '123213',
        ];

        $this->actingAs($user);
        $response = $this->put(route('webhooks.mappings.update', ['webhook' => $mapping->webhook, 'mapping' => $mapping]), $data);

        $response->assertStatus(403);
    }
}
