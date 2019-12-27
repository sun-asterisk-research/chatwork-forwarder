<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Webhook;
use App\Enums\UserType;
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
        $admin = factory(User::class)->create(['role' => 0]);
        $user = factory(User::class)->create();
        factory(Webhook::class)->create(['name' => 'keyword', 'status' => 0, 'user_id' => $user->id]);
        factory(Webhook::class)->create(['name' => 'keyword', 'status' => 1, 'user_id' => $admin->id]);
        factory(Webhook::class)->create(['name' => 'example', 'status' => 0, 'user_id' => $user->id]);
        $searchParams = ['search' => [
            'status' => '0',
            'name' => 'keyword',
            'user' => $user->id
        ]];


        $this->actingAs($admin);
        $response = $this->get(route('admin.webhooks.index', $searchParams));
        $response->assertStatus(200);
        $response->assertViewHas('webhooks');

        $this->assertCount(1, $response->getOriginalContent()->getData()['webhooks']);
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

    public function testAdminCanSeeWebhook()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $webhook = factory(Webhook::class)->create();
        $this->actingAs($admin);

        $response = $this->get(route('admin.webhooks.show', ['webhook' => $webhook]));
        $response->assertStatus(200);
        $response->assertViewHas('webhook');
        $response->assertViewHas('payloads');
        $response->assertViewHas('bot');
    }

    public function testUnauthenticateAdminCannotSeeWebhook()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $webhook = factory(Webhook::class)->create();

        $response = $this->get(route('admin.webhooks.show', ['webhook' => $webhook]));
        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function testUnauthorizationUserCannotSeeWebhook()
    {
        $user = factory(User::class)->create(['role' => UserType::USER]);
        $webhook = factory(Webhook::class)->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.webhooks.show', ['webhook' => $webhook]));
        $response->assertStatus(302);
    }
}
