<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature list admin webhooks without search keyword
     *
     * @return void
     */
    public function testShowListWebhookFeature()
    {
        factory(Webhook::class, 2)->create();
        $user = factory(User::class)->create(['role' => 0]);

        $this->actingAs($user);
        $response = $this->get(route('admin.webhooks.index'));
        $response->assertStatus(200);
        $response->assertViewHas('webhooks');
    }

    /**
     * test Feature list admin webhooks with search keyword
     *
     * @return void
     */
    public function testShowListWebhookWithKeywordFeature()
    {
        factory(Webhook::class, 5)->create(['name' => 'keyword']);
        factory(Webhook::class, 2)->create(['name' => 'example']);
        $user = factory(User::class)->create(['role' => 0]);

        $this->actingAs($user);

        $response = $this->get('/admin/webhooks?search=keyword');
        $response->assertStatus(200);
        $response->assertViewHas('webhooks');

        $this->assertCount(5, $response->getOriginalContent()->getData()['webhooks']);
    }

    /**
     * test Feature list admin webhooks with normal user
     *
     * @return void
     */
    public function testShowListWebhookWithNormalUserFeature()
    {
        factory(Webhook::class, 20)->create();
        $user = factory(User::class)->create(['role' => 1]);

        $this->actingAs($user);
        $response = $this->get('/admin/webhooks');
        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}
