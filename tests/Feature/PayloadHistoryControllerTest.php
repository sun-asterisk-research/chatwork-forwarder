<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Enums\UserType;
use App\Models\MessageHistory;
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
}
