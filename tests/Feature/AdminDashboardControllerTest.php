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

        $chatbot = Bot::create([
            'name' => 'name',
            'bot_key' => 'abc',
            'user_id' => 1
        ]);
        $webhook = Webhook::create([
            'user_id' => 1,
            'bot_id' => $chatbot->id,
            'name' => 'name',
            'token' => Str::random(10),
            'status' => 1,
            'description' => 'description',
            'room_id' => '1',
            'room_name' => 'Room name',
        ]);
        Webhook::create([
            'user_id' => 1,
            'bot_id' => $chatbot->id,
            'name' => 'name',
            'token' => Str::random(10),
            'status' => 0,
            'description' => 'description',
            'room_id' => '1',
            'room_name' => 'Room name',
        ]);

        $payloadHistory = PayloadHistory::create([
            'webhook_id' => $webhook->id,
            'params' => 'params',
            'status' => 1,
            'log' => 'abc'
        ]);
        PayloadHistory::create([
            'webhook_id' => $webhook->id,
            'params' => 'params',
            'status' => 0,
            'log' => 'abc'
        ]);
        MessageHistory::create([
            'payload_history_id' => $payloadHistory->id,
            'message_content' => 'message content',
            'status' => 1,
            'log' => 'log'
        ]);
        MessageHistory::create([
            'payload_history_id' => $payloadHistory->id,
            'message_content' => 'message content',
            'status' => 0,
            'log' => 'log'
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        
        $response->assertViewHas('countData');
        $countDataResponse = $response->getOriginalContent()->getData()['countData'];
        $this->assertTrue(1 == $countDataResponse['user']); 
        $this->assertTrue(1 == $countDataResponse['enabledWebhook']); 
        $this->assertTrue(1 == $countDataResponse['disabledWebhook']); 
        $this->assertTrue(1 == $countDataResponse['bot']);

        $response->assertViewHas('payloadHistory');
        $payloadHistoryResponse = $response->getOriginalContent()->getData()['payloadHistory'];
        $this->assertTrue(1 == $payloadHistoryResponse['failedCases']); 
        $this->assertTrue(1 == $payloadHistoryResponse['successCases']);
        $this->assertTrue(is_array($payloadHistoryResponse['dateChart']));
        $this->assertTrue(is_array($payloadHistoryResponse['payloadFailedChart']));
        $this->assertTrue(1 == $payloadHistoryResponse['payloadFailedChart'][22][1]);
        $this->assertTrue(is_array($payloadHistoryResponse['payloadSuccessChart']));
        $this->assertTrue(1 == $payloadHistoryResponse['payloadSuccessChart'][22][1]);

        $response->assertViewHas('messageHistory');
        $messageHistoryResponse = $response->getOriginalContent()->getData()['messageHistory'];
        $this->assertTrue(1 == $messageHistoryResponse['failedCases']); 
        $this->assertTrue(1 == $messageHistoryResponse['successCases']);
        $this->assertTrue(is_array($messageHistoryResponse['dateChart']));
        $this->assertTrue(is_array($messageHistoryResponse['messageFailedChart']));
        $this->assertTrue(1 == $messageHistoryResponse['messageFailedChart'][22][1]);
        $this->assertTrue(is_array($messageHistoryResponse['messageSuccessChart']));
        $this->assertTrue(1 == $messageHistoryResponse['messageSuccessChart'][22][1]);
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
