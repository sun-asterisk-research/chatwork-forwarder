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
        $perPage = config('paginate.perPage');
        Auth::shouldReceive('user')->once()->andReturn($user);

        $webhookRepo->getAllByUser($perPage);
        $this->assertDatabaseHas('webhooks', ['name' => 'test get all webhook by user']);
    }

    /**
     * get all list webhook for dropdown
     *
     * @return void
     */
    public function testGetAllByUserForDropdown()
    {
        $webhookRepo = new WebhookRepository;

        $webhookLists = factory(Webhook::class, 10)->create(['name' => 'test get all webhook by user']);
        $user = $webhookLists[0]->user;
        $perPage = config('paginate.perPage');
        Auth::shouldReceive('user')->once()->andReturn($user);

        $webhookRepo->getAllByUserForDropDown();
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
     * test update webhook success with once attribute
     *
     * @return void
     */
    public function testUpdateWebhookSuccessWithOnceAttribute()
    {
        $webhook = factory(Webhook::class)->create();
        $webhookRepository = new WebhookRepository;
        $webhookRepository->update($webhook->id, ['name' => 'new name']);

        $this->assertDatabaseHas('webhooks', ['id' => $webhook->id, 'name' => 'new name']);
    }

    /**
     * test update webhook success with multiple attributes
     *
     * @return void
     */
    public function testUpdateWebhookSuccessWithMulAttributes()
    {
        $webhook = factory(Webhook::class)->create();
        $webhookRepository = new WebhookRepository;
        $webhookRepository->update($webhook->id, ['name' => 'new name', 'status' => 0]);

        $this->assertDatabaseHas('webhooks', ['id' => $webhook->id, 'name' => 'new name', 'status' => 0]);
    }

    /**
     * test update webhook fail with webhook not found
     *
     * @return void
     */
    public function testUpdateWebhookFailWithWebhookNotFound()
    {
        $webhookRepository = new WebhookRepository;
        $result = $webhookRepository->update(-1, ['name' => 'new name', 'status' => 0]);

        $this->assertEquals(false, $result);
    }

    /**
     * test getAllAndSearch with keyword
     *
     * @return void
     */
    public function testGetAllAndSearch()
    {
        factory(Webhook::class)->create(['name' => 'keyword', 'status' => 0]);
        factory(Webhook::class)->create(['name' => 'keyword', 'status' => 1]);
        factory(Webhook::class)->create(['name' => 'example', 'status' => 0]);

        $webhookRepository = new WebhookRepository;
        $perPage = config('paginate.perPage');
        $searchParams = ['name' => 'key', 'status' => '0'];
        $result = $webhookRepository->getAllAndSearch($perPage, $searchParams);

        $this->assertCount(1, $result);

        $searchParamsNotFound = ['name' => 'not found', 'status' => '0'];
        $resultNotFound = $webhookRepository->getAllAndSearch($perPage, $searchParamsNotFound);
        $this->assertCount(0, $resultNotFound);
    }

    /**
     * test getAllAndSearch without keyword
     *
     * @return void
     */
    public function testGetAllAndSearchWithoutKeyword()
    {
        factory(Webhook::class, 2)->create();

        $webhookRepository = new WebhookRepository;
        $perPage = config('paginate.perPage');

        $this->assertCount(2, $webhookRepository->getAllAndSearch($perPage, ''));

        $this->assertCount(2, $webhookRepository->getAllAndSearch($perPage, null));
    }
}
