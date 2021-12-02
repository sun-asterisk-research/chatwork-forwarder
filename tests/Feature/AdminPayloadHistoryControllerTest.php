<?php

namespace Tests\Feature;

use App\Enums\PayloadHistoryStatus;
use App\Enums\UserType;
use App\Models\MessageHistory;
use App\Models\PayloadHistory;
use App\Models\User;
use App\Models\Webhook;
use App\Repositories\Eloquents\PayloadHistoryRepository;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AdminPayloadHistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature admin list payload history in page has no record.
     *
     * @return void
     */
    public function testAdminListPayloadHistoryInNoRecordPageFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $webhook = factory(Webhook::class)->create();
        factory(PayloadHistory::class, 2)->create(['webhook_id' => $webhook->id]);
        $this->actingAs($admin);

        $response = $this->get(route('admin.history.index', ['page' => 2]));
        $response->assertLocation(route('admin.history.index', ['page' => 1]));
    }

    /**
     * test Feature admin list payload history success
     *
     * @return void
     */
    public function testAdminListPayloadHistoryFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        factory(PayloadHistory::class, 2)->create(['webhook_id' => $webhook->id]);

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
        $response = $this->get(route('admin.history.index'));
        $response->assertStatus(302);
        $response->assertLocation('/');
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
            'status' => PayloadHistoryStatus::FAILED,
        ]);
        factory(PayloadHistory::class)->create([
            'webhook_id' => $webhook2->id,
            'status' => PayloadHistoryStatus::SUCCESS,
        ]);
        $searchParams = [
            'search' => [
                'status' => 'FAILED',
                'webhook' => $webhook1->id,
            ],
        ];

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
            'status' => PayloadHistoryStatus::SUCCESS,
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

    /**
     * test Feature admin remove payload history successfully.
     *
     * @return void
     */
    public function testRemovePayloadHistoryFeature()
    {
        $payloadHistory = factory(PayloadHistory::class)->create(['params' => 'test remove payload history']);
        factory(MessageHistory::class, 5)->create(['payload_history_id' => $payloadHistory->id]);

        $user = factory(User::class)->create(['role' => 0]);
        $this->actingAs($user);

        $response = $this->delete(route('admin.history.destroy', $payloadHistory));
        $this->assertDatabaseMissing('payload_histories', [
            'id' => $payloadHistory->id,
            'params' => 'test remove payload history',
            'deleted_at' => null,
        ]);
        $this->assertDatabaseMissing('message_histories', ['payload_history_id' => $payloadHistory->id, 'deleted_at' => null]);
        $response->assertRedirect(route('admin.history.index'));
        $response->assertStatus(302);
    }

    /**
     * test Feature admin remove payload history at second page successfully.
     *
     * @return void
     */
    public function testRemovePayloadHistoryAtSecondPageFeature()
    {
        $payloadHistory = factory(PayloadHistory::class)->create(['params' => 'test remove payload history']);
        $admin = factory(User::class)->create(['role' => 0]);
        $this->actingAs($admin);

        $response = $this->delete(route('admin.history.destroy', ['history' => $payloadHistory->id, 'page' => 2]));
        $this->assertDatabaseMissing('payload_histories', [
            'id' => $payloadHistory->id,
            'params' => 'test remove payload history',
            'deleted_at' => null,
        ]);
        $response->assertRedirect(route('admin.history.index', ['page' => 2]));
    }

    /**
     * test Feature admin remove payload history fail.
     *
     * @return void
     */
    public function testRemovePayloadHistoryFailFeature()
    {
        $payloadHistory = factory(PayloadHistory::class)->create(['params' => 'test remove payload history fail']);
        factory(MessageHistory::class, 5)->create(['payload_history_id' => $payloadHistory->id]);
        $user = factory(User::class)->create(['role' => 0]);
        $this->actingAs($user);

        $response = $this->delete(route('admin.history.destroy', $payloadHistory->id + 99));
        $this->assertDatabaseHas('payload_histories', ['id' => $payloadHistory->id, 'params' => 'test remove payload history fail']);
        $this->assertDatabaseHas('message_histories', ['payload_history_id' => $payloadHistory->id]);
        $response->assertStatus(404);
    }

    /**
     * test Feature admin remove payload history unauthorized
     *
     * @return void
     */
    public function testRemovePayloadHistoryUnauthorizedFeature()
    {
        $response = $this->delete(route('admin.history.destroy', 1));

        $response->assertLocation('/');
        $response->assertStatus(302);
    }

    /**
     * test admin remove payload history permission denied
     *
     * @return void
     */
    public function testRemovePayloadHistoryPermissionDenied()
    {
        $payloadHistory = factory(PayloadHistory::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->delete(route('admin.history.destroy', $payloadHistory));
        $response->assertStatus(302);
    }

    public function testRemovePayloadHistoryWithExceptionFeature()
    {
        $payloadHistory = factory(PayloadHistory::class)->create(['params' => 'test remove payload history fail']);
        factory(MessageHistory::class, 5)->create(['payload_history_id' => $payloadHistory->id]);
        $user = factory(User::class)->create(['role' => 0]);
        $this->actingAs($user);

        $mock = Mockery::mock(PayloadHistoryRepository::class);
        $mock->shouldReceive('delete')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(PayloadHistoryRepository::class, $mock);
        $response = $this->delete(route('admin.history.destroy', $payloadHistory));
        $response->assertSessionHas('messageFail', [
            'status' => 'Delete failed',
            'message' => 'Delete failed. Something went wrong',
        ]);
    }
}
