<?php

namespace Tests\Unit\Services;

use Mockery as m;
use Tests\TestCase;
use App\Models\Webhook;
use App\Models\Payload;
use App\Models\Mapping;
use App\Models\Condition;
use App\Models\PayloadHistory;
use App\Models\MessageHistory;
use App\Jobs\SendMessageToChatwork;
use App\Services\ForwardChatworkService;
use App\Repositories\Eloquents\MessageHistoryRepository;
use App\Repositories\Eloquents\PayloadHistoryRepository;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SunAsterisk\Chatwork\Chatwork;
use SunAsterisk\Chatwork\Endpoints\Room;
use SunAsterisk\Chatwork\Endpoints\Rooms\Messages;
use SunAsterisk\Chatwork\Exceptions\APIException;
use App\Enums\MessageHistoryStatus;

class ForwardChatworkServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test generate message matching without mapping
     *
     * @return void
     */
    public function testGenerateMessageMatchingWithoutMapping()
    {
        $webhook = factory(Webhook::class)->create();
        factory(Mapping::class)->create(['webhook_id' => $webhook->id, 'key' => 'qtv', 'value' => '[To:123123] QTV']);
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'Hello, my name is {!! name !!}';
        $params = ['name' => 'qtv'];


        $result = $forwardChatworkService->generateMessage($content, $params);


        $this->assertEquals('Hello, my name is qtv', $result);
    }

    /**
     * test generate message no mapping with field in content is an array
     *
     * @return void
     */
    public function testGenerateMessageNoMappingWithFieldIsArray()
    {
        $webhook = factory(Webhook::class)->create();
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'Hello, my name is {!! student !!}';
        $params = ['student' => ['name' => 'qtv']];


        $result = $forwardChatworkService->generateMessage($content, $params);

        $this->assertEquals('Hello, my name is {"name":"qtv"}', $result);
    }

    /**
     * test generate message no mapping unmatching field
     *
     * @return void
     */
    public function testGenerateMessageNoMappingUnMatching()
    {
        $webhook = factory(Webhook::class)->create();
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'Hello, my name is {!!$params.not_exist!!}';
        $params = json_decode('{"name" : "qtv"}');

        $result = $forwardChatworkService->generateMessage($content, $params);

        $this->assertEquals('', $result);
        $this->assertDatabaseHas('payload_histories', ['log' => 'Not found $params.not_exist ']);
    }

    /**
     * test generate message matching with mapping table
     *
     * @return void
     */
    public function testGenerateMessageMatchingWithMapping()
    {
        $webhook = factory(Webhook::class)->create();
        factory(Mapping::class)->create(['webhook_id' => $webhook->id, 'key' => 'qtv', 'value' => '[To:123123] QTV']);
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'Hello, my name is {{ name }}';
        $params = ['name' => 'qtv'];


        $result = $forwardChatworkService->generateMessage($content, $params);


        $this->assertEquals('Hello, my name is [To:123123] QTV', $result);
    }

    /**
     * test generate message with field in content is an array
     *
     * @return void
     */
    public function testGenerateMessageWithFieldIsArray()
    {
        $webhook = factory(Webhook::class)->create();
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'Hello, my name is {{ student }}';
        $params = ['student' => ['name' => 'qtv']];


        $result = $forwardChatworkService->generateMessage($content, $params);

        $this->assertEquals('Hello, my name is {"name":"qtv"}', $result);
    }

    /**
     * test generate message unmatching with mapping table
     *
     * @return void
     */
    public function testGenerateMessageUnmatchingWithMapping()
    {
        $webhook = factory(Webhook::class)->create();
        factory(Mapping::class)->create(['webhook_id' => $webhook->id, 'key' => 'quangvv', 'value' => '[To:123123] QTV']);
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'Hello, my name is {{ name }}';
        $params = ['name' => 'qtv'];


        $result = $forwardChatworkService->generateMessage($content, $params);


        $this->assertEquals('Hello, my name is qtv', $result);
    }

    /**
     * test generate message unmatching field
     *
     * @return void
     */
    public function testGenerateMessageUnMatching()
    {
        $webhook = factory(Webhook::class)->create();
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'Hello, my name is {{$params.not_exist}}';
        $params = json_decode('{"name" : "qtv"}');

        $result = $forwardChatworkService->generateMessage($content, $params);

        $this->assertEquals('', $result);
        $this->assertDatabaseHas('payload_histories', ['log' => 'Not found $params.not_exist ']);
    }

    /**
     * test generate message matching by regex with mapping table
     *
     * @return void
     */
    public function testGenerateMessageMatchingWithMappingByRegex()
    {
        $webhook = factory(Webhook::class)->create();
        factory(Mapping::class)->create(['webhook_id' => $webhook->id, 'key' => 'qtv', 'value' => '[To:123123] QTV']);
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'This is a message: [[ message ]].';
        $params = ['message' => 'Hello @qtv'];


        $result = $forwardChatworkService->generateMessage($content, $params);


        $this->assertEquals('This is a message: Hello [To:123123] QTV.', $result);
    }

    /**
     * test generate message by regex with field in content is an array, mapping
     *
     * @return void
     */
    public function testGenerateMessageByRegexWithFieldIsArray()
    {
        $webhook = factory(Webhook::class)->create();
        factory(Mapping::class)->create(['webhook_id' => $webhook->id, 'key' => 'qtv', 'value' => '[To:123123] QTV']);
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'This is a message: [[ message ]].';
        $params = ['message' => ['sms' =>  'Hello @qtv']];

        $result = $forwardChatworkService->generateMessage($content, $params);

        $this->assertEquals('This is a message: {"sms":"Hello [To:123123] QTV"}.', $result);
    }

    /**
     * test generate message unmatching by regex with mapping table
     *
     * @return void
     */
    public function testGenerateMessageUnmatchingByRegexWithMapping()
    {
        $webhook = factory(Webhook::class)->create();
        factory(Mapping::class)->create(['webhook_id' => $webhook->id, 'key' => 'quangvv', 'value' => '[To:123123] QTV']);
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'This is a message: [[ message ]].';
        $params = ['message' => 'Hello @qtv'];


        $result = $forwardChatworkService->generateMessage($content, $params);


        $this->assertEquals('This is a message: Hello @qtv.', $result);
    }

    /**
     * test generate message unmatching by regex field
     *
     * @return void
     */
    public function testGenerateMessageUnMatchingByRegex()
    {
        $webhook = factory(Webhook::class)->create();
        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            '',
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );
        $content = 'Hello, my name is [[$params.not_exist]]';
        $params = json_decode('{"name" : "qtv"}');

        $result = $forwardChatworkService->generateMessage($content, $params);

        $this->assertEquals('', $result);
        $this->assertDatabaseHas('payload_histories', ['log' => 'Not found $params.not_exist ']);
    }

    /**
     * test call method found payload
     *
     * @return void
     */
    public function testCallFoundPayload()
    {
        $params = ["name" => 'qtv', 'test' => true, 'is_null' => null];
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => 'name', 'value' => 'qtv']);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => 'test', 'value' => 'true']);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => 'is_null', 'value' => 'null']);

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $forwardChatworkService->call();

        $this->assertDatabaseHas('payload_histories', ['webhook_id' => $webhook->id, 'status' => 0]);
    }

    /**
     * test call method not found payload with one condition
     *
     * @return void
     */
    public function testCallNotFoundPayloadWithOneCondtion()
    {
        $params = ["name" => 'no name'];
        ;
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => 'name', 'value' => 'qtv']);

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $forwardChatworkService->call();

        $this->assertDatabaseHas(
            'payload_histories',
            ['webhook_id' => $webhook->id, 'status' => 1, 'log' => 'This payload does not match any conditions in this webhook.']
        );
    }

    /**
     * test call method not found payload with multiple condition
     *
     * @return void
     */
    public function testCallNotFoundPayloadWithMultiCondition()
    {
        $params = ["name" => 'qtv', "age" => 20];
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => '$params.name', 'value' => 'qtv']);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '>', 'field' => 'asd', 'value' => '30']);

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $forwardChatworkService->call();

        $this->assertDatabaseHas(
            'payload_histories',
            ['webhook_id' => $webhook->id, 'status' => 1, 'log' => 'This payload does not match any conditions in this webhook.']
        );
    }

    public function testCheckCondition()
    {
        $params = ["name" => 'qtv', "age" => 20];
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $conditionEqual = factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => 'name', 'value' => 'qtv']);
        $conditionNotEqual = factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '!=', 'field' => 'name', 'value' => 'qtv']);
        $conditionGreater = factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '>', 'field' => 'name', 'value' => 20]);
        $conditionGreaterOrEqual = factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '>=', 'field' => 'name', 'value' => 20]);
        $conditionLess = factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '<', 'field' => 'name', 'value' => 10]);
        $conditionLessOrEqual = factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '<=', 'field' => 'name', 'value' => 10]);
        $conditionNotExist = factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '<>', 'field' => 'name', 'value' => 10]);

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $this->assertEquals(true, $forwardChatworkService->checkCondition('qtv', $conditionEqual));
        $this->assertEquals(false, $forwardChatworkService->checkCondition('asd', $conditionEqual));
        $this->assertEquals(true, $forwardChatworkService->checkCondition('tvq', $conditionNotEqual));
        $this->assertEquals(false, $forwardChatworkService->checkCondition('qtv', $conditionNotEqual));
        $this->assertEquals(true, $forwardChatworkService->checkCondition(21, $conditionGreater));
        $this->assertEquals(false, $forwardChatworkService->checkCondition(20, $conditionGreater));
        $this->assertEquals(true, $forwardChatworkService->checkCondition(20, $conditionGreaterOrEqual));
        $this->assertEquals(false, $forwardChatworkService->checkCondition(19, $conditionGreaterOrEqual));
        $this->assertEquals(true, $forwardChatworkService->checkCondition(9, $conditionLess));
        $this->assertEquals(false, $forwardChatworkService->checkCondition(11, $conditionLess));
        $this->assertEquals(true, $forwardChatworkService->checkCondition(9, $conditionLessOrEqual));
        $this->assertEquals(true, $forwardChatworkService->checkCondition(10, $conditionLessOrEqual));
        $this->assertEquals(false, $forwardChatworkService->checkCondition(11, $conditionLessOrEqual));
        $this->assertEquals(false, $forwardChatworkService->checkCondition(11, $conditionNotExist));
    }

    /**
     * test send message success
     *
     * @return void
     */
    public function testSendMessagesSuccess()
    {
        $params = json_decode('{"name" : "qtv", "age" : 20}');
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $payloadHistory = factory(PayloadHistory::class)->create();

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $mock = m::mock(Chatwork::class);
        $room = m::mock(Room::class);
        $messages = m::mock(Messages::class);
        $messages->shouldReceive('create')->andReturn('123123');
        $room->shouldReceive('messages')->andReturn($messages);
        $mock->shouldReceive('room')->andReturn($room);

        $response = $forwardChatworkService->sendMessages(['asd', 'vxcv'], $mock, $payloadHistory->id);

        $this->assertDatabaseHas('message_histories', ['payload_history_id' => $payloadHistory->id, 'status' => MessageHistoryStatus::SUCCESS]);
    }

    /**
     * test send message failed by permission
     *
     * @return void
     */
    public function testSendMessagesPermission()
    {
        $params = json_decode('{"name" : "qtv", "age" : 20}');
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $payloadHistory = factory(PayloadHistory::class)->create();

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $mock = m::mock(Chatwork::class);
        $mock->shouldReceive('room')->andThrow(new APIException(403, ['errors' => ['Permission']]));

        $response = $forwardChatworkService->sendMessages(['asd'], $mock, $payloadHistory->id);

        $this->assertDatabaseHas('message_histories', ['log' => 'Permission']);
    }

    /**
     * test send message method failed by limit request
     *
     * @return void
     */
    public function testSendMessagesLimitRequest()
    {
        Queue::fake();
        $params = json_decode('{"name" : "qtv", "age" : 20}');
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $payloadHistory = factory(PayloadHistory::class)->create();

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $mock = m::mock(Chatwork::class);
        $mock->shouldReceive('room')->andThrow(new APIException(429, ['errors' => ['Permission']]));

        $response = $forwardChatworkService->sendMessages(['asd'], $mock, $payloadHistory->id);

        Queue::assertPushed(SendMessageToChatwork::class);
    }
}
