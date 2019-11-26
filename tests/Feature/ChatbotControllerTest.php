<?php

namespace Tests\Feature;

use App\Models\Chatbot;
use Tests\TestCase;
use App\Models\Bot;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature list bots.
     *
     * @return void
     */
    public function testShowListChatbotFeature()
    {
        $chatbotList = factory(Bot::class, 2)->create();
        $user = $chatbotList[0]->user;

        $this->actingAs($user);
        $response = $this->get('/bots');
        $response->assertStatus(200);
        $response->assertViewHas('bots');
    }
}
