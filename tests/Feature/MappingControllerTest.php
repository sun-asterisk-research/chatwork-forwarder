<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Webhook;
use App\Models\Mapping;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $this->assertDatabaseMissing('mappings', ['id' => $mapping->id, 'deleted_at' => NULL]);
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
}
