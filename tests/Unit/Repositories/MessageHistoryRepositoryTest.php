<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\MessageHistory;
use App\Models\PayloadHistory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Eloquents\MessageHistoryRepository;

class MessageHistoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * get model
     *
     * @return void
     */
    public function testGetModel()
    {
        $messageHistoryRepo = new MessageHistoryRepository;

        $data = $messageHistoryRepo->getModel();
        $this->assertEquals(MessageHistory::class, $data);
    }

    /**
     * get all and search Message History
     *
     * @return void
     */
    public function testGetAllAndSearch()
    {
        $perPage = config('paginate.perPage');
        $messageHistoryRepo = new MessageHistoryRepository;
        $payloadHistory = factory(PayloadHistory::class)->create();

        factory(MessageHistory::class, 5)->create(['message_content' => 'something', 'payload_history_id' => $payloadHistory->id]);
        $messageHistories = factory(MessageHistory::class, 10)->create(['message_content' => 'test', 'payload_history_id' => $payloadHistory->id]);

        // keyword: test
        $result = $messageHistoryRepo->getAllAndSearch($messageHistories[0]->payloadHistory->id, 'test');

        $this->assertCount(10, $result);

        // keyword: test2
        $result = $messageHistoryRepo->getAllAndSearch($messageHistories[0]->payloadHistory->id, 'test2');

        $this->assertCount(0, $result);

        // keyword: null
        $result = $messageHistoryRepo->getAllAndSearch($messageHistories[0]->payloadHistory->id, null);

        $this->assertCount(15, $result);
    }
}
