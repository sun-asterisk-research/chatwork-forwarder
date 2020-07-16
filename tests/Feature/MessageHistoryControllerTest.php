<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MessageHistory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Exception;
use App\Repositories\Interfaces\MessageHistoryRepositoryInterface as MessageHistoryRepository;
use App\Enums\UserType;

class MessageHistoryControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
    * test Feature remove message history successfully.
    *
    * @return void
    */
   public function testRemoveMessageHistoryFeature()
   {
       $messageHistory = factory(MessageHistory::class)->create(['message_content' => 'test remove message history']);
       $user = $messageHistory->payloadHistory->webhook->user;
       $payloadHistoryId = $messageHistory->payloadHistory->id;
       $this->actingAs($user);
       $response = $this->delete(route('message.destroy', $messageHistory));
       $this->assertDatabaseMissing('message_histories', [
           'id' => $messageHistory->id,
           'message_content' => 'test remove message history',
           'deleted_at' => NULL,
        ]);
       $response->assertRedirect(route('history.show', ['history' => $payloadHistoryId]));
       $response->assertStatus(302);
   }

   /**
   * test Feature remove message history raise exception
   *
   * @return void
   */
   public function testRemoveMessageHistoryFailRaiseException()
   {
       $messageHistory = factory(MessageHistory::class)->create(['message_content' => 'test remove message history']);
       $user = $messageHistory->payloadHistory->webhook->user;
       $payloadHistoryId = $messageHistory->payloadHistory->id;
       $this->actingAs($user);

       $mock = Mockery::mock(MessageHistoryRepository::class);
       $mock->shouldReceive('delete')->andThrowExceptions([new Exception('Exception', 100)]);
       $this->app->instance(MessageHistoryRepository::class, $mock);

       $response = $this->delete(route('message.destroy', $messageHistory));

       $response->assertSessionHas('messageFail', [
           'status' => 'Delete failed',
           'message' => 'Delete failed. Something went wrong',
       ]);
   }

   /**
    * test Feature remove message history fail.
    *
    * @return void
    */
   public function testRemoveMessageHistoryFailFeature()
   {
       $messageHistory = factory(MessageHistory::class)->create(['message_content' => 'test remove message history fail']);
       $user = $messageHistory->payloadHistory->webhook->user;

       $this->actingAs($user);
       $response = $this->delete(route('message.destroy', $messageHistory->id + 99));
       $this->assertDatabaseHas('message_histories', ['message_content' => 'test remove message history fail']);
       $response->assertStatus(404);
   }

   /**
    * test Feature remove message history unauthorized
    *
    * @return void
    */
   public function testRemoveMessageHistoryUnauthorizedFeature()
   {
       $response = $this->delete(route('message.destroy', 1));

       $response->assertLocation('/');
       $response->assertStatus(302);
   }

    /**
    * test remove message history permission denied
    *
    * @return void
    */
    public function testRemoveMessageHistoryPermissionDenied()
    {
        $messageHistory = factory(MessageHistory::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->delete(route('message.destroy', $messageHistory));

        $response->assertStatus(403);
    }

    /**
    * test admin account can remove message history
    *
    * @return void
    */
    public function testAdminCanRemoveMessageHistory()
    {
        $messageHistory = factory(MessageHistory::class)->create(['message_content' => 'test remove message history']);
        $payloadHistoryId = $messageHistory->payloadHistory->id;
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($admin);

        $response = $this->delete(route('message.destroy', $messageHistory));
        $this->assertDatabaseMissing('message_histories', [
            'id' => $messageHistory->id,
            'message_content' => 'test remove message history',
            'deleted_at' => NULL,
         ]);
        $response->assertRedirect(route('history.show', ['history' => $payloadHistoryId]));
        $response->assertStatus(302);
    }
}
