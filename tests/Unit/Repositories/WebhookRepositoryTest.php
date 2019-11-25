<?php

namespace Tests\Unit\Repositories;

use Auth;
use Mockery;
use Tests\TestCase;
use App\Models\Webhook;
use App\Repositories\Eloquents\WebhookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebhookRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * get model
     *
     * @return void
     */
    public function testGetModel()
    {
        $webhookRepo = new WebhookRepository;

        $data = $webhookRepo->getModel();
        $this->assertEquals(Webhook::class, $data);
    }

    /**
     * get all list webhook by user
     *
     * @return void
     */
    public function testGetAllByUser()
    {
        $webhookRepo = new WebhookRepository;

        $webhookLists = factory(Webhook::class, 10)->create(['name' => 'test get all webhook by user']);
        $user = $webhookLists[0]->user;

        Auth::shouldReceive('user')->once()->andReturn($user);

        $webhookRepo->getAllByUser();
        $this->assertDatabaseHas('webhooks', ['name' => 'test get all webhook by user']);
    }

    /**
     * test create webhook in repository
     *
     * @return void
     */
    public function testCreateWebhook()
    {
        $webhookRepo = new WebhookRepository;

        $data = ['name' => 'string', 'description' => 'string', 'bot_id' => 1, 'room_name' => 'string', 'room_id' => 1, 'user_id' => 1, 'token' => 'asadasdsadsads'];

        $webhookRepo->create($data);
        $this->assertDatabaseHas('webhooks', ['name' => 'string']);
    }

    /**
     * unit test store webhook controller count called function (create, auth)
     */
    public function testStoreWebhook()
    {
        $this->withoutMiddleware();
        // mock instance Repository
        $mock = Mockery::mock('App\Repositories\Interfaces\WebhookRepositoryInterface');

        $this->app->instance('App\Repositories\Interfaces\WebhookRepositoryInterface', $mock);

        $auth = Auth::shouldReceive('id')->andReturn(1);
        $create = $mock->shouldReceive('create');
        $response = $this->post('/webhooks', ['name' => 'string', 'description' => 'some thing successfully', 'bot_id' => 1, 'room_name' => 'string', 'room_id' => 1]);

        $create->once();
        $auth->times(3);
    }
}
