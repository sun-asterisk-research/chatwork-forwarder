<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\PayloadHistory;
use App\Models\Webhook;
use App\Models\User;
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

    /**
     * get all and search by user Payload History
     *
     * @return void
     */
    public function testgetAllByUserAndSearch()
    {
        $payloadHistoryRepo = new PayloadHistoryRepository;
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        factory(PayloadHistory::class)->create();
        factory(PayloadHistory::class)->create([
            'webhook_id' => $webhook->id
        ]);
        $this->actingAs($user);
        $searchParams = ['webhook' => $webhook->id];
        $searchParamsNotFound = ['webhook' => -1];

        $perPage = config('paginate.perPage');
        $result = $payloadHistoryRepo->getAllByUserAndSearch($perPage, $searchParams);

        $this->assertCount(1, $result);

        $resultNotFound = $payloadHistoryRepo->getAllByUserAndSearch($perPage, $searchParamsNotFound);
        $this->assertCount(0, $resultNotFound);

        $result = $payloadHistoryRepo->getAllByUserAndSearch($perPage, null);
        $this->assertCount(1, $result);
    }

    /**
     * get find Payload History
     *
     * @return void
     */
    public function testgetAllAndSearch()
    {
        $payloadHistoryRepo = new PayloadHistoryRepository;
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        factory(PayloadHistory::class)->create();
        factory(PayloadHistory::class)->create([
            'webhook_id' => $webhook->id
        ]);
        $this->actingAs($user);
        $searchParams = ['webhook' => $webhook->id];
        $searchParamsNotFound = ['webhook' => -1];

        $perPage = config('paginate.perPage');
        $result = $payloadHistoryRepo->getAllAndSearch($perPage, $searchParams);

        $this->assertCount(1, $result);

        $resultNotFound = $payloadHistoryRepo->getAllAndSearch($perPage, $searchParamsNotFound);
        $this->assertCount(0, $resultNotFound);

        $result = $payloadHistoryRepo->getAllAndSearch($perPage, null);
        $this->assertCount(2, $result);
    }
}
