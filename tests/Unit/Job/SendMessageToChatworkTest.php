<?php

namespace Tests\Unit\Job;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SunAsterisk\Chatwork\Chatwork;
use SunAsterisk\Chatwork\Endpoints\Room;
use SunAsterisk\Chatwork\Endpoints\Rooms\Messages;
use App\Jobs\SendMessageToChatwork;
use App\Enums\MessageHistoryStatus;
use SunAsterisk\Chatwork\Exceptions\APIException;

class SendMessageToChatworkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test job send message success
     *
     * @return void
     */
    public function testSendMessageSuccess()
    {
        $chatwork = \Mockery::mock(Chatwork::class);
        $room = \Mockery::mock(Room::class);
        $messages = \Mockery::mock(Messages::class);

        $chatwork->shouldReceive('room')->andReturn($room);
        $room->shouldReceive('messages')->andReturn($messages);
        $messages->shouldReceive('create')->andReturn(123);

        $sendMessageJob = new SendMessageToChatwork($chatwork, 123, ['asd'], 1);

        $sendMessageJob->handle();

        $this->assertDatabaseHas('message_histories', ['status' => MessageHistoryStatus::SUCCESS]);
    }

    /**
     * test job send message failed
     *
     * @return void
     */
    public function testSendMessageFailed()
    {
        $chatwork = \Mockery::mock(Chatwork::class);

        $chatwork->shouldReceive('room')->andThrow(new APIException(429, ['errors' => ['Limit Request']]));

        $sendMessageJob = new SendMessageToChatwork($chatwork, 123, ['asd'], 1);

        $sendMessageJob->handle();

        $this->assertDatabaseHas('message_histories', ['status' => MessageHistoryStatus::FAILED]);
    }
}
