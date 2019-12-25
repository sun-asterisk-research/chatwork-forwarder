<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\Eloquents\ChatworkRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use SunAsterisk\Chatwork\Chatwork;
use SunAsterisk\Chatwork\Endpoints\Rooms;

class ChatworkRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test get rooms
     *
     * @return void
     */
    public function testGetRooms()
    {
        $chatworkRepository = new ChatworkRepository;
        $rooms = [
            [
                "room_id" => 12345678,
                "name" => "Ruby ez gem",
                "type" => "group",
                "role" => "member",
                "icon_path" => "https://chatwork.com/avatar/some_avatar.png",
            ],
            [
                "room_id" => 12345679,
                "name" => "Chatwork forwarder",
                "type" => "group",
                "role" => "member",
                "sticky" => false,
                "icon_path" => "https://chatwork.com/avatar/some_avatar.png",
            ],
            [
                "room_id" => 12345610,
                "name" => "Phan Dang Hai Vu (97)",
                "type" => "direct",
                "role" => "member",
                "icon_path" => "https://chatwork.com/avatar/some_avatar.rsz.png",
            ],
            [
                "room_id" => 12345611,
                "name" => "Vo Van Quang 2k",
                "type" => "direct",
                "role" => "member",
                "icon_path" => "https://chatwork.com/avatar/some_avatar.rsz.jpg",
            ]
        ];
        $roomsApi = m::mock(Rooms::class);
        $mock = m::mock(Chatwork::class);
        $roomsApi->shouldReceive('list')->andReturn($rooms);
        $mock->shouldReceive('rooms')->andReturn($roomsApi);
        $this->app->instance(Rooms::class, $roomsApi);

        $data = $chatworkRepository->getRooms($mock);
        $this->assertEquals([$rooms[0], $rooms[1]], $data);
    }
}
