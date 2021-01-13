<?php

namespace Tests\Unit\Models;

use App\Enums\MessageHistoryStatus;
use App\Models\MessageHistory;
use App\Models\PayloadHistory;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_contains_valid_fillable_properties()
    {
        $fillable = [
            'payload_history_id',
            'message_content',
            'status',
            'log',
        ];
        $model = new MessageHistory();

        $this->assertEquals($fillable, $model->getFillable());
    }

    public function test_webhook_relation()
    {
        $model = new MessageHistory();
        $belongToModel = $model->payloadHistory();

        $this->assertInstanceOf(BelongsTo::class, $belongToModel);
    }

    /**
     * test scope data chart
     *
     * @return void
     */
    public function test_scopeDataChart()
    {
        $success = MessageHistoryStatus::SUCCESS;
        $failed = MessageHistoryStatus::FAILED;

        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => $success]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 12:00:00'), 'status' => $success]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 23:59:59'), 'status' => $success]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => $failed]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $success]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $failed]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $success]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $failed]);

        // fromDate = toDate and status is SUCCESS
        $result = MessageHistory::dataChart(['fromDate' => '2019-12-01', 'toDate' => '2019-12-01'], $success);
        $this->assertEquals(3, $result['01-12-2019']);
        // fromDate = toDate and status is FAILED
        $result = MessageHistory::dataChart(['fromDate' => '2019-12-01', 'toDate' => '2019-12-01'], $failed);
        $this->assertEquals(1, $result['01-12-2019']);

        // fromDate != toDate and status is SUCCESS
        $result = MessageHistory::dataChart(['fromDate' => '2019-12-01', 'toDate' => '2019-12-03'], $success);
        $this->assertEquals(3, $result['01-12-2019']);
        $this->assertEquals(1, $result['02-12-2019']);
        $this->assertEquals(1, $result['03-12-2019']);
        // fromDate != toDate and status is FAILED
        $result = MessageHistory::dataChart(['fromDate' => '2019-12-01', 'toDate' => '2019-12-03'], $failed);
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
        $payloadHistory = factory(PayloadHistory::class)->create(['webhook_id' => $webhook->id]);
        $anotherPayloadHistory = factory(PayloadHistory::class)->create(['webhook_id' => $anotherWebhook->id]);
        $success = MessageHistoryStatus::SUCCESS;
        $failed = MessageHistoryStatus::FAILED;

        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => $success, 'payload_history_id' => $payloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 12:00:00'), 'status' => $success, 'payload_history_id' => $payloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 23:59:59'), 'status' => $success, 'payload_history_id' => $payloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => $failed, 'payload_history_id' => $payloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 23:59:59'), 'status' => $success, 'payload_history_id' => $anotherPayloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-01 00:00:00'), 'status' => $failed, 'payload_history_id' => $anotherPayloadHistory->id]);

        factory(MessageHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $success, 'payload_history_id' => $payloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $failed, 'payload_history_id' => $payloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $success, 'payload_history_id' => $anotherPayloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-02 00:00:00'), 'status' => $failed, 'payload_history_id' => $anotherPayloadHistory->id]);

        factory(MessageHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $success, 'payload_history_id' => $payloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $failed, 'payload_history_id' => $payloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $success, 'payload_history_id' => $anotherPayloadHistory->id]);
        factory(MessageHistory::class)->create(['created_at' => date('2019-12-03 00:00:00'), 'status' => $failed, 'payload_history_id' => $anotherPayloadHistory->id]);

        // fromDate = toDate and status is SUCCESS
        $result = MessageHistory::dataChartByUser(['fromDate' => '2019-12-01', 'toDate' => '2019-12-01'], $success, $user->id);
        $this->assertEquals(3, $result['01-12-2019']);
        // fromDate = toDate and status is FAILED
        $result = MessageHistory::dataChartByUser(['fromDate' => '2019-12-01', 'toDate' => '2019-12-01'], $failed, $user->id);
        $this->assertEquals(1, $result['01-12-2019']);

        // fromDate != toDate and status is SUCCESS
        $result = MessageHistory::dataChartByUser(['fromDate' => '2019-12-01', 'toDate' => '2019-12-03'], $success, $user->id);
        $this->assertEquals(3, $result['01-12-2019']);
        $this->assertEquals(1, $result['02-12-2019']);
        $this->assertEquals(1, $result['03-12-2019']);
        // fromDate != toDate and status is FAILED
        $result = MessageHistory::dataChartByUser(['fromDate' => '2019-12-01', 'toDate' => '2019-12-03'], $failed, $user->id);
        $this->assertEquals(1, $result['01-12-2019']);
        $this->assertEquals(1, $result['02-12-2019']);
        $this->assertEquals(1, $result['03-12-2019']);
    }
}
