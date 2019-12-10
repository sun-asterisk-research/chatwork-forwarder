<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\PayloadHistory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Eloquents\PayloadHistoryRepository;

class PayloadHistoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * get model
     *
     * @return void
     */
    public function testGetModel()
    {
        $payloadHistoryRepo = new PayloadHistoryRepository;

        $data = $payloadHistoryRepo->getModel();
        $this->assertEquals(PayloadHistory::class, $data);
    }

    /**
     * get find Payload History
     *
     * @return void
     */
    public function testFind()
    {
        $payloadHistoryRepo = new PayloadHistoryRepository;

        $payloadHistories = factory(PayloadHistory::class, 10)->create(['params' => 'test find payload history']);

        $payloadHistoryRepo->find($payloadHistories[0]->id);

        $this->assertDatabaseHas('payload_histories', ['params' => 'test find payload history']);
    }
}
