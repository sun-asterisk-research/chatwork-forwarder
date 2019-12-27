<?php

namespace Tests\Feature\Models;

use App\Models\User;
use App\Models\Bot;
use App\Models\Webhook;
use App\Models\Payload;
use App\Models\Mapping;
use App\Models\PayloadHistory;
use App\Enums\WebhookStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function testContainsValidFillableProperties()
    {
        $fillable = [
            'user_id',
            'bot_id',
            'name',
            'token',
            'status',
            'description',
            'room_id',
            'room_name',
        ];
        $model = new Webhook();

        $this->assertEquals($fillable, $model->getFillable());
    }

    public function testWebhookBeLongsToUser()
    {
        $user = factory(User::class)->create(); 
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]); 
        
        $this->assertEquals('user_id', $webhook->user()->getForeignKeyName());
        $this->assertInstanceOf(BelongsTo::class, $webhook->user());
    }

    public function testWebhookBeLongsToBot()
    {
        $bot = factory(Bot::class)->create(); 
        $webhook = factory(Webhook::class)->create(['bot_id' => $bot->id]); 
        
        $this->assertEquals('bot_id', $webhook->bot()->getForeignKeyName());
        $this->assertInstanceOf(BelongsTo::class, $webhook->bot());
    }

    public function testWebhookHasManyPayloads()
    {
        $webhook = factory(Webhook::class)->create();
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]); 
       
        $this->assertInstanceOf(HasMany::class, $webhook->payloads());
        $this->assertEquals('webhook_id', $webhook->payloads()->getForeignKeyName());
    }

    public function testWebhookHasManyMapping()
    {
        $webhook = factory(Webhook::class)->create();
        $mapping = factory(Mapping::class)->create(['webhook_id' => $webhook->id]);
       
        $this->assertInstanceOf(HasMany::class, $webhook->mappings());
        $this->assertEquals('webhook_id', $webhook->mappings()->getForeignKeyName());
    }

    public function testWebhookHasManyPayloadHistory()
    {
        $webhook = factory(Webhook::class)->create();
        $payloadHistory = factory(PayloadHistory::class)->create(['webhook_id' => $webhook->id]);
       
        $this->assertInstanceOf(HasMany::class, $webhook->payloadHistories());
        $this->assertEquals('webhook_id', $webhook->payloadHistories()->getForeignKeyName());
    }

    public function testScopeEnable()
    {
        $webhook = factory(Webhook::class)->create(['status' => WebhookStatus::ENABLED]);
        $result = Webhook::enable();

        $this->assertEquals(1, $result->count());
    }

    public function testScopeDisabled()
    {
        $webhook = factory(Webhook::class)->create(['status' => WebhookStatus::DISABLED]);
        $result = Webhook::disable();

        $this->assertEquals(1, $result->count());
    }

    public function testScopeByUser()
    {
        $user = factory(User::class)->create();
        factory(Webhook::class, 2)->create(['user_id' => $user->id]);
        factory(Webhook::class)->create();

        $result = Webhook::byUser($user->id);

        $this->assertEquals(2, $result->count());
    }

    public function testScopeSearch()
    {
        $perPage = config('paginate.perPage');
        $webhook = factory(Webhook::class)->create();
        factory(Webhook::class, 2)->create(['name' => 'abc']);
        $searchParams['name'] = $webhook->name;
        $searchParams['status'] = $webhook->status;
        $searchParams['user'] = $webhook->user->id;
        $result = Webhook::search($searchParams, $perPage);

        $this->assertEquals(1, $result->count());
    }

    public function testUsingSoftDeleted()
    {
        $webhook = new Webhook();

        $this->assertContains('deleted_at', $webhook->getDates());
    }
}
