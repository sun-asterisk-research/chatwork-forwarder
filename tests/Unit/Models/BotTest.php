<?php

namespace Tests\Feature\Models;

use App\Models\Bot;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BotTest extends TestCase
{
    use RefreshDatabase;

    public function test_contains_valid_fillable_properties()
    {
        $fillable = [
            'user_id',
            'name',
            'type',
            'bot_key'
        ];
        $model = new Bot();

        $this->assertEquals($fillable, $model->getFillable());
    }

    public function test_webhook_relation()
    {
        $model = new Bot();
        $relation = $model->webhooks();

        $this->assertInstanceOf(HasMany::class, $relation);
    }

    /**
     * test scope get mapping by key
     *
     * @return void
     */
    public function testScopeByUser()
    {
        $user = factory(User::class)->create();
        factory(Bot::class, 2)->create(['user_id' => $user->id]);
        factory(Bot::class)->create();

        $result = Bot::byUser($user->id);

        $this->assertEquals(2, $result->count());
    }

    /**
     * test using softDeletes
     *
     * @return void
     */
    public function testUsingSoftDeleted()
    {
        $bot = new Bot();

        $this->assertContains('deleted_at', $bot->getDates());
    }
}
