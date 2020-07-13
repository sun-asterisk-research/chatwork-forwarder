<?php

namespace Tests\Feature;

use App\Models\Condition;
use Tests\TestCase;
use App\Models\User;
use App\Models\Webhook;
use App\Models\Payload;
use App\Enums\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Exception;
use Illuminate\Database\QueryException;
use App\Repositories\Interfaces\PayloadRepositoryInterface as PayloadRepository;

class PayloadControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature remove payload successfully.
     *
     * @return void
     */
    public function testRemovePayloadFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'test remove payload']);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload' => $payload]));
        $this->assertDatabaseMissing('payloads', [
            'id' => $payload->id,
            'content' => 'test remove payload',
            'deleted_at' => null,
        ]);
        $response->assertRedirect(route('webhooks.edit', $webhook));
        $response->assertStatus(302);
    }

    /**
     * test Feature remove payload raise exception
     *
     * @return void
     */
    public function testRemovePayloadFailRaiseException()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'test remove payload']);

        $this->actingAs($user);

        $mock = Mockery::mock(PayloadRepository::class);
        $mock->shouldReceive('delete')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(PayloadRepository::class, $mock);

        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload' => $payload]));

        $response->assertSessionHas('messageFail', [
            'status' => 'Delete failed',
            'message' => 'Delete failed. Something went wrong',
        ]);
    }

    /**
     * test Feature remove payload fail.
     *
     * @return void
     */
    public function testRemovePayloadFailFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'test remove payload fail']);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload_id' => ($payload->id + 99)]));
        $this->assertDatabaseHas('payloads', ['content' => 'test remove payload fail']);
        $response->assertStatus(404);
    }

    /**
     * test Feature remove payload fail when it has condition(s).
     *
     * @return void
     */
    public function testRemovePayloadHasConditionFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'test remove payload fail']);
        factory(Condition::class)->create(['payload_id' => $payload->id]);

        $this->actingAs($user);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload_id' => $payload->id]));
        $this->assertDatabaseHas('payloads', ['content' => 'test remove payload fail']);
        $response->assertStatus(302);
        $response->assertSessionHas('messageFail', [
            'status' => 'Delete failed',
            'message' => 'This payload has some conditions to be related with, please delete them first',
        ]);
    }

    /**
     * test Feature remove payload unauthorized
     *
     * @return void
     */
    public function testRemovePayloadUnauthorizedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'test remove payload fail']);
        $response = $this->delete(route('webhooks.payloads.destroy', ['webhook' => $webhook, 'payload_id' => 1]));

        $response->assertLocation('/');
        $response->assertStatus(302);
    }

    /**
     * test Feature show create payload view successfully
     *
     * @return void
     */
    public function testShowCreateViewPayloadFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get(route('webhooks.payloads.create', $webhook));
        $response->assertStatus(200);
        $response->assertViewHas('webhook');
    }

    /**
     * test Feature show create payload view when user not authorized
     *
     * @return void
     */
    public function testShowCreateViewPayloadUnauthorizedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);

        $response = $this->get(route('webhooks.payloads.create', $webhook));
        $response->assertLocation('/');
        $response->assertStatus(302);
    }

    /**
     * test Feature show create payload view when webhook not exist
     *
     * @return void
     */
    public function testShowCreateViewPayloadOfWebhookNotExistFeature()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get(route('webhooks.payloads.create', -1));
        $response->assertStatus(404);
    }

    /**
     * test Feature store payload successfully
     *
     * @return void
     */
    public function testStorePayloadSuccessFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->post(route('webhooks.payloads.store', $webhook), [
            'content' => 'Hi my name is {{name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'fields' => ['name', 'age'],
            'operators' => ['==', '>='],
            'values' => ['rammus', '30']
        ]);
        $response->assertStatus(200);
        $response->assertSessionHas('messageSuccess', [
            'status' => 'Create success',
            'message' => 'This payload successfully created',
        ]);
    }

    public function testStorePayloadFailException()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $mock = Mockery::mock(PayloadRepository::class);
        $mock->shouldReceive('create')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(PayloadRepository::class, $mock);

        $response = $this->post(route('webhooks.payloads.store', $webhook), [
            'content' => 'Hi my name is {{name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'fields' => ['name', 'age'],
            'operators' => ['==', '>='],
            'values' => ['rammus', '30']
        ]);

        $response->assertSessionHas('messageFail', [
            'status' => 'Create failed',
            'message' => 'Create failed. Something went wrong',
        ]);
    }

    /**
     * test Feature create payload raise exception
     *
     * @return void
     */
    public function testCreatePayloadFailedRaiseException()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $mock = Mockery::mock(PayloadRepository::class);
        $mock->shouldReceive('create')->andThrowExceptions([new QueryException('', [], new Exception)]);
        $this->app->instance(PayloadRepository::class, $mock);

        $response = $this->post(route('webhooks.payloads.store', $webhook), [
            'content' => 'Hi my name is {{name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'fields' => ['name', 'age'],
            'operators' => ['==', '>='],
            'values' => ['rammus', '30']
        ]);

        $this->assertEquals(0, Payload::all()->count());
        $this->assertEquals(0, Condition::all()->count());
    }

    /**
     * test Feature store payload with condition fields not match with params
     *
     * @return void
     */
    public function testStorePayloadInvalidFieldsFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->post(route('webhooks.payloads.store', $webhook), [
            'content' => '',
            'params' => '{"name": "rasmus", "age": "30"}',
            'fields' => ['asd', 'age'],
            'operators' => ['==', '>'],
            'values' => ['rammus', '30']
        ]);
        $errors = session('errors')->toArray();

        $response->assertStatus(302);
        $this->assertEquals($errors['fields'][0], ['field0' => 'This field is not match with params']);
        $this->assertEquals($errors['content'][0], 'Please enter content');
    }

    /**
     * test Feature store payload with message content not match with params
     *
     * @return void
     */
    public function testStorePayloadInvalidContentFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->post(route('webhooks.payloads.store', $webhook), [
            'content' => 'Hi my name is {{$payload->asd}}',
            'params' => '{"name": "rasmus", "age": "30"}',
        ]);
        $errors = session('errors')->toArray();

        $response->assertStatus(302);
        $this->assertEquals($errors['content'][0], '$payload->asd not found in params');
    }

    /**
     * test Feature store payload violate validation rule
     *
     * @return void
     */
    public function testStorePayloadFailedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->post(route('webhooks.payloads.store', $webhook), [
            'content' => '',
            'fields' => ['name', 'age'],
            'operators' => ['==', '>'],
            'values' => ['rammus', '30']
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'content' => 'Please enter content',
            'params' => 'Please enter payload params to validate the conditions',
        ]);
    }

    /**
     * test Feature store payload with not existed webhook
     *
     * @return void
     */
    public function testStorePayloadOfWebhookNotExistFeature()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->post(route('webhooks.payloads.store', -1), [
            'content' => 'sample content',
            'params' => '{"name": "rasmus", "age": "30"}',
            'fields' => ['name', 'age'],
            'operators' => ['==', '>='],
            'values' => ['rammus', '30']
        ]);
        $response->assertStatus(404);
    }

    /**
     * test Feature show payload detail success
     *
     * @return void
     */
    public function testShowPayloadDetailSuccessFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        factory(Condition::class)->create(['payload_id' => $payload->id]);
        $this->actingAs($user);

        $response = $this->get(route('webhooks.payloads.edit', ['webhook' => $webhook, 'payload' => $payload]));
        $response->assertStatus(200);
        $response->assertViewHas(['webhook', 'payload', 'conditions']);
    }

    /**
     * test Feature show payload detail when user not login
     *
     * @return void
     */
    public function testShowPayloadDetailUnauthorizedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $response = $this->get(route('webhooks.payloads.edit', ['webhook' => $webhook, 'payload' => $payload]));

        $response->assertLocation('/');
        $response->assertStatus(302);
    }

    /**
     * test Feature show payload does not exist
     *
     * @return void
     */
    public function testShowNotExistPayloadDetailFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->get(route('webhooks.payloads.edit', ['webhook' => $webhook, 'payload' => -1]));

        $response->assertStatus(404);
    }

    /**
     * test Feature payload is updated successfully
     *
     * @return void
     */
    public function testUpdatePayloadSuccessFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'old content']);
        $this->actingAs($user);
        $response = $this->put(route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]), [
            'content' => 'Hi my name is {{name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
        ]);
        $payload = Payload::find($payload->id);

        $response->assertStatus(200);
        $response->assertSessionHas('messageSuccess', [
            'status' => 'Update success',
            'message' => 'This payload successfully updated',
        ]);
        $this->assertEquals($payload->content, 'Hi my name is {{name}}');
    }

    public function testUpdatePayloadFailException()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'old content']);
        $this->actingAs($user);

        $mock = Mockery::mock(PayloadRepository::class);
        $mock->shouldReceive('create')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(PayloadRepository::class, $mock);

        $response = $this->put(route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]), [
            'content' => 'Hi my name is {{name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
        ]);

        $response->assertSessionHas('messageFail', [
            'status' => 'Update failed',
            'message' => 'Update failed. Something went wrong',
        ]);
    }

    /**
     * test Feature update payload raise exception
     *
     * @return void
     */
    public function testUpdatePayloadFailedRaiseException()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'old content']);
        $this->actingAs($user);

        $mock = Mockery::mock(PayloadRepository::class);
        $mock->shouldReceive('update')->andThrowExceptions([new QueryException('', [], new Exception)]);
        $this->app->instance(PayloadRepository::class, $mock);

        $response = $this->put(route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]), [
            'content' => 'Hi my name is {{name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
        ]);

        $this->assertEquals('old content', Payload::first()->content);
    }

    /**
     * test Feature payload with its conditions are updated successfully
     *
     * @return void
     */
    public function testUpdatePayloadWithConditionsSuccessFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id, 'content' => 'old content']);
        $condition = factory(Condition::class)->create([
            'payload_id' => $payload->id,
            'field' => 'name',
            'operator' => '==',
            'value' => 'rasmus',
        ]);
        $params = [
            'content' => 'New content',
            'ids' => [$condition->id],
            'params' => '{"name": "rasmus", "age": "30"}',
            'fields' => ['age'],
            'conditions' => [
                [
                    'id' => $condition->id,
                    'field' => 'age',
                    'operator' => '>=',
                    'value' => '18'
                ],
                [
                    'id' => '',
                    'field' => '$params->age',
                    'operator' => '>=',
                    'value' => '30'
                ]
            ]
        ];
        $this->actingAs($user);
        $response = $this->put(route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]), $params);
        $condition = Condition::find($condition->id);

        $response->assertStatus(200);
        $response->assertSessionHas('messageSuccess', [
            'status' => 'Update success',
            'message' => 'This payload successfully updated',
        ]);
        $this->assertEquals($condition->field, $params['conditions'][0]['field']);
        $this->assertEquals($condition->operator, $params['conditions'][0]['operator']);
        $this->assertEquals($condition->value, $params['conditions'][0]['value']);
    }

    /**
     * test Feature updating payload when violating validation rule
     *
     * @return void
     */
    public function testUpdatePayloadFailedFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $this->actingAs($user);
        $response = $this->put(route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]), [
            'content' => '',
            'params' => ''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'content' => 'Please enter content',
            'params' => 'Please enter payload params to validate the conditions',
        ]);
    }

    /**
     * test Feature updating payload with invalid condition fields
     *
     * @return void
     */
    public function testUpdatePayloadInvalidFieldFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $this->actingAs($user);
        $response = $this->put(route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]), [
            'params' => '{"name": "rasmus", "age": "30"}',
            'fields' => ['name', 'asd'],
        ]);
        $errors = session('errors')->toArray();

        $response->assertStatus(302);
        $this->assertEquals($errors['fields'][0], ['field1' => 'This field is not match with params']);
    }

    /**
     * test Feature updating payload with invalid message content
     *
     * @return void
     */
    public function testUpdatePayloadInvalidContentFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $payload = factory(Payload::class)->create(['webhook_id' => $webhook->id]);
        $this->actingAs($user);
        $response = $this->put(route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]), [
            'params' => '{"name": "rasmus", "age": "30"}',
            'content' => 'Hi my name is {{$payload->asd}}',
        ]);
        $errors = session('errors')->toArray();

        $response->assertStatus(302);
        $this->assertEquals($errors['content'][0], '$payload->asd not found in params');
    }

    /**
     * test Feature updating an payload does not exist
     *
     * @return void
     */
    public function testUpdatePayloadNotExistFeature()
    {
        $user = factory(User::class)->create();
        $webhook = factory(Webhook::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->put(route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => -1]), [
            'content' => 'New content'
        ]);

        $response->assertStatus(404);
    }
}
