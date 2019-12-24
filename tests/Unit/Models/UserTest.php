<?php

namespace Tests\Feature\Models;

use App\Models\User;
use App\Models\Bot;
use App\Models\Webhook;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testContainsValidFillableProperties()
    {
        $fillable = [
            'name',
            'email',
            'password',
            'role',
            'avatar',
        ];
        $model = new User();

        $this->assertEquals($fillable, $model->getFillable());
    }

    public function testUserHasManyBots()
    {
        $user = factory(User::class)->create(); 
        $bot = factory(Bot::class)->create(['user_id' => $user->id]); 
       
        $this->assertInstanceOf(HasMany::class, $user->bots());
        $this->assertEquals('user_id', $user->bots()->getForeignKeyName());
    }

    public function testUserHasManyWebhooks()
    {
        $user = factory(User::class)->create(); 
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]); 
       
        $this->assertInstanceOf(HasMany::class, $user->webhooks());
        $this->assertEquals('user_id', $user->webhooks()->getForeignKeyName());
    }

    /**
     * test scope search
     *
     * @return void
     */
    public function testScopeSearch()
    {
        $perPage = config('paginate.perPage');
        $user = factory(User::class)->create();
        factory(User::class, 2)->create(['name' => 'abc']);
        $searchParams['name'] = $user->name;
        $searchParams['email'] = $user->email;

        $result = User::search($searchParams, $perPage);

        $this->assertEquals(1, $result->count());
    }

    /**
     * test using softDeletes
     *
     * @return void
     */
    public function testUsingSoftDeleted()
    {
        $user = new User();

        $this->assertContains('deleted_at', $user->getDates());
    }
}
