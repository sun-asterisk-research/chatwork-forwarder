<?php

namespace Tests\Unit\Repositories;

use Auth;
use Mockery;
use Tests\TestCase;
use App\Models\Bot;
use App\Models\User;
use App\Repositories\Eloquents\BotRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BotRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * get model
     *
     * @return void
     */
    public function testGetModel()
    {
        $botRepository = new BotRepository;

        $data = $botRepository->getModel();
        $this->assertEquals(Bot::class, $data);
    }

    /**
     * get all list bot by user
     *
     * @return void
     */
    public function testGetAllByUser()
    {
        $botRepository = new BotRepository;
        $botLists = factory(Bot::class, 10)->create(['name' => 'test get all bot by user']);
        $user = $botLists[0]->user;
        Auth::shouldReceive('user')->once()->andReturn($user);

        $botRepository->getAllByUser();
        $this->assertDatabaseHas('bots', ['name' => 'test get all bot by user']);
    }

    /**
     * test create bot
     *
     * @return void
     */
    public function testCreateBot()
    {
        $botRepository = new BotRepository;
        $user = factory(User::class)->create();
        $attributes = [
            'name' => 'Bot Name',
            'cw_id' => '123123',
            'bot_key' => 'asdad234saddr2sdfsasd',
            'user_id' => $user->id
        ];

        $botRepository->create($attributes);

        $this->assertDatabaseHas('bots', ['name' => 'Bot Name', 'user_id' => $user->id]);
    }

}
