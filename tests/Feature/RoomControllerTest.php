<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Bot;
use App\Repositories\Eloquents\ChatworkRepository;
use SunAsterisk\Chatwork\Endpoints\Room;
use Mockery as m;

class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature get room with invalid bot key.
     *
     * @return void
     */
    // public function testGetRoomSuccess()
    // {
    //     $user = factory(User::class)->create();
    //     $bot = factory(Bot::class)->create(['bot_key' => 'asdasdasdasdasd', 'user_id' => $user->id]);
    //     $room1 = m::mock(Room::class);
    //     $room2 = m::mock(Room::class);
    //     $this->actingAs($user);
    //     $mock = m::mock(ChatworkRepository::class);
    //     $mock->shouldReceive('getRooms')->andReturn([$room1, $room2]);
    //     $this->app->instance(ChatworkRepository::class, $mock);

    //     $response = $this->get(route('rooms.index', ['bot_id' => $bot->id]));
    //     $this->assertEquals($response->getOriginalContent(), [$room1, $room2]);
    // }

    /**
     * test Feature get room with invalid bot key.
     *
     * @return void
     */
    // public function testGetRoomInvalidBotKeyFeature()
    // {
    //     $user = factory(User::class)->create();
    //     $bot = factory(Bot::class)->create(['bot_key' => 'asdasdasdasdasd', 'user_id' => $user->id]);
    //     $this->actingAs($user);
    //     $response = $this->get(route('rooms.index', ['bot_id' => $bot->id]));
    //     $this->assertEquals($response->getOriginalContent(), []);
    // }

    /**
     * test Feature get room with invalid bot key.
     *
     * @return void
     */
    public function testUserUnauthorizedFeature()
    {
        $user = factory(User::class)->create();
        $bot = factory(Bot::class)->create(['bot_key' => 'asdasdasdasdasd']);
        $this->actingAs($user);
        $response = $this->get(route('rooms.index', ['bot_id' => $bot->id]));

        $response->assertStatus(403);
    }
}
