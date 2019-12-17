<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Webhook;
use App\Models\Payload;
use App\Models\Condition;
use App\Services\ForwardChatworkService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Eloquents\PayloadHistoryRepository;
use App\Repositories\Eloquents\MessageHistoryRepository;
use App\Models\Mapping;

class ForwardChatworkServiceTest extends TestCase
{
    use RefreshDatabase;

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
        $content = 'Hello, my name is {{ $params->name }}';
        $params = json_decode('{"name" : "qtv"}');


        $result = $forwardChatworkService->generateMessage($content, $params);


        $this->assertEquals('Hello, my name is [To:123123] QTV', $result);
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
        $content = 'Hello, my name is {{ $params->name }}';
        $params = json_decode('{"name" : "qtv"}');


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
        $content = 'Hello, my name is {{$params->not_exist}}';
        $params = json_decode('{"name" : "qtv"}');

        $result = $forwardChatworkService->generateMessage($content, $params);

        $this->assertEquals('', $result);
        $this->assertDatabaseHas('payload_histories', ['log' => 'Not found $params->not_exist ']);
    }

    /**
     * test call method found payload
     *
     * @return void
     */
    public function testCallFoundPayload()
    {
        $params = json_decode('{"name" : "qtv"}');
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => '$params->name', 'value' => 'qtv']);

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
        $params = json_decode('{"name" : "no name"}');
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => '$params->name', 'value' => 'qtv']);

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $forwardChatworkService->call();

        $this->assertDatabaseHas('payload_histories', ['webhook_id' => $webhook->id, 'status' => 1, 'log' => 'Not found payload.']);
    }

    /**
     * test call method not found payload with multiple condition
     *
     * @return void
     */
    public function testCallNotFoundPayloadWithMultiCondition()
    {
        $params = json_decode('{"name" : "qtv", "age" : 20}');
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '==', 'field' => '$params->name', 'value' => 'qtv']);
        factory(Condition::class)->create(['payload_id' => $payload->id, 'operator' => '>', 'field' => '$params->age', 'value' => '30']);

        $forwardChatworkService = new ForwardChatworkService(
            $webhook,
            $params,
            new PayloadHistoryRepository(),
            new MessageHistoryRepository()
        );

        $forwardChatworkService->call();

        $this->assertDatabaseHas('payload_histories', ['webhook_id' => $webhook->id, 'status' => 1, 'log' => 'Not found payload.']);
    }
}
