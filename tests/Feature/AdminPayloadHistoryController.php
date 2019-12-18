<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Enums\UserType;
use App\Models\Webhook;
use App\Models\MessageHistory;
use App\Models\PayloadHistory;
use App\Enums\PayloadHistoryStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPayloadHistoryController extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature admin list payload history success
     *
     * @return void
     */
    public function testAdminListPayloadHistoryFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);

        $response = $this->get(route('admin.history.index'));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $response->assertViewHas('webhooks');
        $response->assertViewHas('payloadHistoryStatuses');
        $this->assertCount(2, $responsePayloadHistories);
    }

    /**
     * test Feature admin list payload history Unauthorization.
     *
     * @return void
     */
    public function testAdminListPayloadHistoryUnauthorizationFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $response = $this->get(route('admin.history.index'));
        $response->assertStatus(302);
        $response->assertLocation('/login');
    }

    /**
     * test Feature admin list payload history Unauthenticated.
     *
     * @return void
     */
    public function testAdminListPayloadHistoryUnauthenticatedFeature()
    {
        $user = factory(User::class)->create(['role' => UserType::USER]);
        $this->actingAs($user);

        $response = $this->get(route('admin.history.index'));
        $response->assertStatus(302);
    }

    /**
     * test Feature admin search payload history by webhook.
     *
     * @return void
     */
    public function testAdminSearchPayloadHistoryByWebhookFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $webhook1 = factory(Webhook::class)->create();
        $webhook2 = factory(Webhook::class)->create();
        $expectPayloadHistory = factory(PayloadHistory::class)->create(['webhook_id' => $webhook1->id]);
        factory(PayloadHistory::class)->create(['webhook_id' => $webhook2->id]);
        $searchParams = ['search' => ['webhook' => $webhook1->id]];

        $this->actingAs($admin);
        $response = $this->get(route('admin.history.index', $searchParams));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertCount(1, $responsePayloadHistories);
        $this->assertEquals($expectPayloadHistory->id, $responsePayloadHistories[0]->id);
    }

    /**
     * test Feature admin search payload history by status.
     *
     * @return void
     */
    public function testAdminSearchPayloadHistoryByStatusFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $webhook = factory(Webhook::class)->create(['user_id' => $admin->id]);
        $expectPayloadHistory = factory(PayloadHistory::class)
            ->create(['webhook_id' => $webhook->id, 'status' => PayloadHistoryStatus::FAILED]);
        factory(PayloadHistory::class)->create(['webhook_id' => $webhook->id, 'status' => PayloadHistoryStatus::SUCCESS]);
        $searchParams = ['search' => ['status' => 'FAILED']];

        $this->actingAs($admin);
        $response = $this->get(route('admin.history.index', $searchParams));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertEquals($expectPayloadHistory->id, $responsePayloadHistories[0]->id);
    }

    /**
     * test Feature admin search payload history by webhook and status.
     *
     * @return void
     */
    public function testAdminSearchPayloadHistoryByWebhookAndStatusFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $webhook1 = factory(Webhook::class)->create();
        $webhook2 = factory(Webhook::class)->create();
        $expectPayloadHistory = factory(PayloadHistory::class)->create([
            'webhook_id' => $webhook1->id,
            'status' => PayloadHistoryStatus::FAILED
        ]);
        factory(PayloadHistory::class)->create([
            'webhook_id' => $webhook2->id,
            'status' => PayloadHistoryStatus::SUCCESS
        ]);
        $searchParams = ['search' => [
            'status' => 'FAILED',
            'webhook' => $webhook1->id
        ]];

        $this->actingAs($admin);
        $response = $this->get(route('admin.history.index', $searchParams));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertEquals($expectPayloadHistory->id, $responsePayloadHistories[0]->id);
    }

    /**
     * test Feature admin search payload history not found.
     *
     * @return void
     */
    public function testAdminSearchPayloadHistoryNotFoundFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $webhook1 = factory(Webhook::class)->create();
        $webhook2 = factory(Webhook::class)->create();
        factory(PayloadHistory::class)->create([
            'webhook_id' => $webhook2->id,
            'status' => PayloadHistoryStatus::SUCCESS
        ]);
        $searchParams = ['search' => ['webhook' => $webhook1->id]];

        $this->actingAs($admin);
        $response = $this->get(route('admin.history.index', $searchParams));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertCount(0, $responsePayloadHistories);
        $response->assertSeeText('No matching records found');
    }

    /**
     * test Feature show admin payload history.
     *
     * @return void
     */
    public function testShowAdminDetailPayloadHistoryFeature()
    {
        $messageHistories = factory(MessageHistory::class, 2)->create();
        $user = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($user);
        $response = $this->get(route('admin.history.show', ['history' => $messageHistories[0]->payloadHistory->id]));
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistory');
        $response->assertViewHas('messageHistories');
    }

    /**
     * test Feature show admin payload history fail with normal user.
     *
     * @return void
     */
    public function testShowAdminDetailPayloadHistoryOtherUserFeature()
    {
        $messageHistories = factory(MessageHistory::class, 2)->create();
        $user = $messageHistories[0]->payloadHistory->webhook->user;

        $this->actingAs($user);
        $response = $this->get(route('admin.history.show', ['history' => $messageHistories[0]->payloadHistory->id]));
        $response->assertStatus(302);
    }

    /**
     * test Feature show admin payload history fail with normal another user.
     *
     * @return void
     */
    public function testShowAdminDetailPayloadHistoryAdminUserFeature()
    {
        $messageHistories = factory(MessageHistory::class, 2)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->get(route('admin.history.show', ['history' => $messageHistories[0]->payloadHistory->id]));
        $response->assertStatus(302);
    }
}
