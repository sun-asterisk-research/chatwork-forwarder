<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\UserType;
use App\Models\PayloadHistory;
use App\Models\MessageHistory;
use App\Models\Webhook;
use App\Models\User;
use App\Models\Bot;
use Illuminate\Support\Str;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
    * test Feature Admin Dashboard.
    *
    * @return void
    */
    public function testAdminDashboardFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $user = factory(User::class)->create(['role' => UserType::USER]);
        $chatbot = factory(Bot::class)->create(['user_id' => $user->id]);

        $webhook = factory(Webhook::class)->create(['user_id' => $user->id, 'bot_id' => $chatbot->id, 'status' => 0]);
        factory(Webhook::class)->create(['user_id' => $user->id, 'bot_id' => $chatbot->id,'status' => 1]);

        $payloadHistory = factory(PayloadHistory::class)->create(['webhook_id' => $webhook->id, 'status' => 1]);
        factory(PayloadHistory::class)->create(['webhook_id' => $webhook->id, 'status' => 0]);

        $messageHistory = factory(MessageHistory::class)->create(['payload_history_id' => $payloadHistory->id, 'status' => 1]);
        factory(MessageHistory::class)->create(['payload_history_id' => $payloadHistory->id, 'status' => 0]);

        $this->actingAs($admin);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        $response->assertViewHas('countData');
        $countDataResponse = $response->getOriginalContent()->getData()['countData'];
        $this->assertEquals(2, $countDataResponse['user']); 
        $this->assertEquals(1, $countDataResponse['enabledWebhook']); 
        $this->assertEquals(1, $countDataResponse['disabledWebhook']); 
        $this->assertEquals(1, $countDataResponse['bot']);

        $response->assertViewHas('payloadHistory');
        $payloadHistoryResponse = $response->getOriginalContent()->getData()['payloadHistory'];
        $indexLastArray = count($payloadHistoryResponse['payloadFailedChart']) - 1;
        $this->assertEquals(1, $payloadHistoryResponse['failedCases']); 
        $this->assertEquals(1, $payloadHistoryResponse['successCases']);
        $this->assertTrue(is_array($payloadHistoryResponse['dateChart']));
        $this->assertTrue(is_array($payloadHistoryResponse['payloadFailedChart']));
        $this->assertEquals(1, $payloadHistoryResponse['payloadFailedChart'][$indexLastArray][1]);
        $this->assertTrue(is_array($payloadHistoryResponse['payloadSuccessChart']));
        $this->assertEquals(1, $payloadHistoryResponse['payloadSuccessChart'][$indexLastArray][1]);

        $response->assertViewHas('messageHistory');
        $messageHistoryResponse = $response->getOriginalContent()->getData()['messageHistory'];
        $this->assertEquals(1, $messageHistoryResponse['failedCases']); 
        $this->assertEquals(1, $messageHistoryResponse['successCases']);
        $this->assertTrue(is_array($messageHistoryResponse['dateChart']));
        $this->assertTrue(is_array($messageHistoryResponse['messageFailedChart']));
        $this->assertEquals(1, $messageHistoryResponse['messageFailedChart'][$indexLastArray][1]);
        $this->assertTrue(is_array($messageHistoryResponse['messageSuccessChart']));
        $this->assertEquals(1, $messageHistoryResponse['messageSuccessChart'][$indexLastArray][1]);
    }

    /**
    * test Feature Admin Dashboard Unauthenticated.
    *
    * @return void
    */
    public function testUnauthenticatedAdminDashboardFeature()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302);
        $response->assertLocation('/');
    }

    /**
    * test Feature Admin Dashboard Unauthorization.
    *
    * @return void
    */
    public function testUnauthorizationAdminDashboardFeature()
    {
        $user = factory(User::class)->create(['role' => UserType::USER]);

        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302);
        $response->assertLocation('/');
    }
}
