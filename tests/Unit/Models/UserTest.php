<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

        $this->assertInstanceOf(HasMany::class, $user->bots());
        $this->assertEquals('user_id', $user->bots()->getForeignKeyName());
    }

    public function testUserHasManyWebhooks()
    {
        $user = factory(User::class)->create();

        $this->assertInstanceOf(HasMany::class, $user->webhooks());
        $this->assertEquals('user_id', $user->webhooks()->getForeignKeyName());
    }

    public function testUserHasManyTemplates()
    {
        $user = factory(User::class)->create();

        $this->assertInstanceOf(HasMany::class, $user->templates());
        $this->assertEquals('user_id', $user->templates()->getForeignKeyName());
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

        $searchParams = [
            'name' => $user->name,
            'email' => $user->email,
        ];

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
