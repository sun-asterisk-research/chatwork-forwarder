<?php

namespace Tests\Feature\Models;

use App\Models\PayloadHistory;
use App\Models\Webhook;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\PayloadHistoryStatus;

class PayloadHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_contains_valid_fillable_properties()
    {
        $fillable = [
            'webhook_id', 'params', 'status', 'log'
        ];
        $model = new PayloadHistory();

        $this->assertEquals($fillable, $model->getFillable());
    }

    public function test_webhook_relation()
    {
        $model = new PayloadHistory();
        $belongToModel = $model->webhook();

        $this->assertInstanceOf(BelongsTo::class, $belongToModel);
    }

    public function test_message_histories_relation()
    {
        $model = new PayloadHistory();
        $hasManyModel = $model->messageHistories();

        $this->assertInstanceOf(HasMany::class, $hasManyModel);
    }

    public function test_scopeSearch()
    {
        $perPage = config('paginate.perPage');
        $webhook = factory(Webhook::class)->create();
        $anotherWebhook = factory(Webhook::class)->create();

        factory(PayloadHistory::class, 5)->create(['webhook_id' => $webhook->id, 'status' => PayloadHistoryStatus::SUCCESS]);
        factory(PayloadHistory::class, 10)->create(['webhook_id' => $webhook->id, 'status' => PayloadHistoryStatus::FAILED]);
        factory(PayloadHistory::class, 5)->create(['webhook_id' => $anotherWebhook->id, 'status' => PayloadHistoryStatus::SUCCESS]);

        // Don't have searchParams
        $result = PayloadHistory::search(['webhook' => '', 'status' => ''], $perPage);
        $this->assertEquals(15, $result->count());

        // searchParams have only status
        $result = PayloadHistory::search(['webhook' => '', 'status' => 'SUCCESS'], $perPage);
        $this->assertEquals(10, $result->count());
        $result = PayloadHistory::search(['webhook' => '', 'status' => 'FAILED'], $perPage);
        $this->assertEquals(10, $result->count());

        // searchParams have only webhook id
        $result = PayloadHistory::search(['webhook' => $webhook->id, 'status' => ''], $perPage);
        $this->assertEquals(15, $result->count());

        // searchParams have status and webhook id
        $result = PayloadHistory::search(['webhook' => $webhook->id, 'status' => 'SUCCESS'], $perPage);
        $this->assertEquals(5, $result->count());
    }

    /**
     * test scope data chart
     *
     * @return void
     */
    public function test_scopeDataChart()
    {
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => PayloadHistoryStatus::SUCCESS]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 12:00:00'), 'status' => PayloadHistoryStatus::SUCCESS]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 23:59:59'), 'status' => PayloadHistoryStatus::SUCCESS]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => PayloadHistoryStatus::FAILED]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => PayloadHistoryStatus::SUCCESS]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => PayloadHistoryStatus::FAILED]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => PayloadHistoryStatus::SUCCESS]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => PayloadHistoryStatus::FAILED]);

        // fromDate = toDate and status is SUCCESS
        $result = PayloadHistory::dataChart(['fromDate' => '2019-12-01', 'toDate' => '2019-12-01'], PayloadHistoryStatus::SUCCESS);
        $this->assertEquals(3, $result['01-12-2019']);
        // fromDate = toDate and status is FAILED
        $result = PayloadHistory::dataChart(['fromDate' => '2019-12-01', 'toDate' => '2019-12-01'], PayloadHistoryStatus::FAILED);
        $this->assertEquals(1, $result['01-12-2019']);

        // fromDate != toDate and status is SUCCESS
        $result = PayloadHistory::dataChart(['fromDate' => '2019-12-01', 'toDate' => '2019-12-03'], PayloadHistoryStatus::SUCCESS);
        $this->assertEquals(3, $result['01-12-2019']);
        $this->assertEquals(1, $result['02-12-2019']);
        $this->assertEquals(1, $result['03-12-2019']);
        // fromDate != toDate and status is FAILED
        $result = PayloadHistory::dataChart(['fromDate' => '2019-12-01', 'toDate' => '2019-12-03'], PayloadHistoryStatus::FAILED);
        $this->assertEquals(1, $result['01-12-2019']);
        $this->assertEquals(1, $result['02-12-2019']);
        $this->assertEquals(1, $result['03-12-2019']);
    }

    /**
     * test scope data chart by user
     *
     * @return void
     */
    public function test_scopeDataChartByUser()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $anotherWebhook = factory(Webhook::class)->create();
        $success = PayloadHistoryStatus::SUCCESS;
        $failed = PayloadHistoryStatus::FAILED;

        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => $success, 'webhook_id' => $webhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 12:00:00'), 'status' => $success, 'webhook_id' => $webhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 23:59:59'), 'status' => $success, 'webhook_id' => $webhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => $failed, 'webhook_id' => $webhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 23:59:59'), 'status' => $success, 'webhook_id' => $anotherWebhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => $failed, 'webhook_id' => $anotherWebhook->id]);

        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $success, 'webhook_id' => $webhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $failed, 'webhook_id' => $webhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $success, 'webhook_id' => $anotherWebhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $failed, 'webhook_id' => $anotherWebhook->id]);

        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $success, 'webhook_id' => $webhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $failed, 'webhook_id' => $webhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $success, 'webhook_id' => $anotherWebhook->id]);
        factory(PayloadHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $failed, 'webhook_id' => $anotherWebhook->id]);

        // fromDate = toDate and status is SUCCESS
        $result = PayloadHistory::dataChartByUser(['fromDate' => '2019-12-01', 'toDate' => '2019-12-01'], $success, $user->id);
        $this->assertEquals(3, $result['01-12-2019']);
        // fromDate = toDate and status is FAILED
        $result = PayloadHistory::dataChartByUser(['fromDate' => '2019-12-01', 'toDate' => '2019-12-01'], $failed, $user->id);
        $this->assertEquals(1, $result['01-12-2019']);

        // fromDate != toDate and status is SUCCESS
        $result = PayloadHistory::dataChartByUser(['fromDate' => '2019-12-01', 'toDate' => '2019-12-03'], $success, $user->id);
        $this->assertEquals(3, $result['01-12-2019']);
        $this->assertEquals(1, $result['02-12-2019']);
        $this->assertEquals(1, $result['03-12-2019']);
        // fromDate != toDate and status is FAILED
        $result = PayloadHistory::dataChartByUser(['fromDate' => '2019-12-01', 'toDate' => '2019-12-03'], $failed, $user->id);
        $this->assertEquals(1, $result['01-12-2019']);
        $this->assertEquals(1, $result['02-12-2019']);
        $this->assertEquals(1, $result['03-12-2019']);
    }
}
