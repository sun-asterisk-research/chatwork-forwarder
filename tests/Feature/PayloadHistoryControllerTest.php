<?php

namespace Tests\Feature;

use App\Enums\PayloadHistoryStatus;
use Tests\TestCase;
use App\Models\User;
use App\Enums\UserType;
use App\Models\MessageHistory;
use App\Models\PayloadHistory;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayloadHistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature show payload history.
     *
     * @return void
     */
    public function testShowDetailPayloadHistoryFeature()
    {
        $messageHistories = factory(MessageHistory::class, 2)->create();
        $user = $messageHistories[0]->payloadHistory->webhook->user;

        $this->actingAs($user);
        $response = $this->get(route('history.show', ['history' => $messageHistories[0]->payloadHistory->id]));
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistory');
        $response->assertViewHas('messageHistories');
    }

    /**
     * test Feature show payload history fail with another user.
     *
     * @return void
     */
    public function testShowDetailPayloadHistoryOtherUserFeature()
    {
        $messageHistories = factory(MessageHistory::class, 2)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->get(route('history.show', ['history' => $messageHistories[0]->payloadHistory->id]));
        $response->assertStatus(403);
    }

    /**
     * test Feature show payload history fail with admin user.
     *
     * @return void
     */
    public function testShowDetailPayloadHistoryAdminUserFeature()
    {
        $messageHistories = factory(MessageHistory::class, 2)->create();
        $user = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($user);
        $response = $this->get(route('history.show', ['history' => $messageHistories[0]->payloadHistory->id]));
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistory');
        $response->assertViewHas('messageHistories');
    }

    /**
     * test Feature list payload history success.
     *
     * @return void
     */
    public function testListPayloadHistoryFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        factory(PayloadHistory::class, 2)->create(['webhook_id' => $webhook->id]);

        $this->actingAs($user);
        $response = $this->get(route('history.index'));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertCount(2, $responsePayloadHistories);
    }

    /**
     * test Feature list payload history when user not login.
     *
     * @return void
     */
    public function testListPayloadHistoryUnauthenticatedFeature()
    {
        factory(PayloadHistory::class, 2)->create();

        $response = $this->get(route('history.index'));
        $response->assertStatus(302);
        $response->assertLocation('/login');
    }

    /**
     * test Feature search payload history by webhook.
     *
     * @return void
     */
    public function testSearchPayloadHistoryByWebhookFeature()
    {
        $user = factory(User::class)->create();
        $webhook1 = factory(Webhook::class)->create(['user_id' => $user->id]);
        $webhook2 = factory(Webhook::class)->create(['user_id' => $user->id]);
        $expectPayloadHistory = factory(PayloadHistory::class)->create(['webhook_id' => $webhook1->id]);
        factory(PayloadHistory::class)->create(['webhook_id' => $webhook2->id]);
        $searchParams = ['search' => ['webhook' => $webhook1->id]];

        $this->actingAs($user);
        $response = $this->get(route('history.index', $searchParams));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertCount(1, $responsePayloadHistories);
        $this->assertEquals($expectPayloadHistory->id, $responsePayloadHistories[0]->id);
    }

    /**
     * test Feature search payload history by status.
     *
     * @return void
     */
    public function testSearchPayloadHistoryByStatusFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $expectPayloadHistory = factory(PayloadHistory::class)
            ->create(['webhook_id' => $webhook->id, 'status' => PayloadHistoryStatus::FAILED]);
        factory(PayloadHistory::class)->create(['webhook_id' => $webhook->id, 'status' => PayloadHistoryStatus::SUCCESS]);
        $searchParams = ['search' => ['status' => 'FAILED']];

        $this->actingAs($user);
        $response = $this->get(route('history.index', $searchParams));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertCount(1, $responsePayloadHistories);
        $this->assertEquals($expectPayloadHistory->id, $responsePayloadHistories[0]->id);
    }

    /**
     * test Feature search payload history by webhook and status.
     *
     * @return void
     */
    public function testSearchPayloadHistoryByWebhookAndStatusFeature()
    {
        $user = factory(User::class)->create();
        $webhook1 = factory(Webhook::class)->create(['user_id' => $user->id]);
        $webhook2 = factory(Webhook::class)->create(['user_id' => $user->id]);
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

        $this->actingAs($user);
        $response = $this->get(route('history.index', $searchParams));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertCount(1, $responsePayloadHistories);
        $this->assertEquals($expectPayloadHistory->id, $responsePayloadHistories[0]->id);
    }

    /**
     * test Feature search payload history not found.
     *
     * @return void
     */
    public function testSearchPayloadHistoryNotFoundFeature()
    {
        $user = factory(User::class)->create();
        $webhook1 = factory(Webhook::class)->create(['user_id' => $user->id]);
        $webhook2 = factory(Webhook::class)->create(['user_id' => $user->id]);
        factory(PayloadHistory::class)->create([
            'webhook_id' => $webhook2->id,
            'status' => PayloadHistoryStatus::SUCCESS
        ]);
        $searchParams = ['search' => ['webhook' => $webhook1->id]];

        $this->actingAs($user);
        $response = $this->get(route('history.index', $searchParams));
        $responsePayloadHistories = $response->getOriginalContent()->getData()['payloadHistories'];
        $response->assertStatus(200);
        $response->assertViewHas('payloadHistories');
        $this->assertCount(0, $responsePayloadHistories);
        $response->assertSeeText('No matching records found');
    }

    /**
    * test Feature remove payload history successfully.
    *
    * @return void
    */
   public function testRemovePayloadHistoryFeature()
   {
       $payloadHistory = factory(PayloadHistory::class)->create(['params' => 'test remove payload history']);
       factory(MessageHistory::class, 5)->create(['payload_history_id' => $payloadHistory->id]);

       $user = $payloadHistory->webhook->user;
       $this->actingAs($user);

       $response = $this->delete(route('history.destroy', $payloadHistory));
       $this->assertDatabaseMissing('payload_histories', [
           'id' => $payloadHistory->id,
           'params' => 'test remove payload history',
           'deleted_at' => NULL,
        ]);
       $this->assertDatabaseMissing('message_histories', ['payload_history_id' => $payloadHistory->id, 'deleted_at' => NULL]);
       $response->assertRedirect(route('history.index'));
       $response->assertStatus(302);
   }

   /**
    * test Feature remove payload history fail.
    *
    * @return void
    */
   public function testRemovePayloadHistoryFailFeature()
   {
       $payloadHistory = factory(PayloadHistory::class)->create(['params' => 'test remove payload history fail']);
       factory(MessageHistory::class, 5)->create(['payload_history_id' => $payloadHistory->id]);
       $user = $payloadHistory->webhook->user;
       $this->actingAs($user);

       $response = $this->delete(route('history.destroy', $payloadHistory->id + 99));
       $this->assertDatabaseHas('payload_histories', ['id' => $payloadHistory->id, 'params' => 'test remove payload history fail']);
       $this->assertDatabaseHas('message_histories', ['payload_history_id' => $payloadHistory->id]);
       $response->assertStatus(404);
   }

   /**
    * test Feature remove payload history unauthorized
    *
    * @return void
    */
   public function testRemovePayloadHistoryUnauthorizedFeature()
   {
       $response = $this->delete(route('history.destroy', 1));

       $response->assertLocation('/login');
       $response->assertStatus(302);
   }

    /**
    * test remove payload history permission denied
    *
    * @return void
    */
    public function testRemovePayloadHistoryPermissionDenied()
    {
        $payloadHistory = factory(PayloadHistory::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->delete(route('history.destroy', $payloadHistory));

        $response->assertStatus(403);
    }
}
