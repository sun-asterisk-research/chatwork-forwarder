<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MessageHistory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminMessageHistoryController extends TestCase
{
    use RefreshDatabase;
    /**
    * test Feature admin remove message history successfully.
    *
    * @return void
    */
   public function testRemoveMessageHistoryFeature()
   {
       $messageHistory = factory(MessageHistory::class)->create(['message_content' => 'test remove message history']);
       $user = factory(User::class)->create(['role' => 0]);
       $payloadHistoryId = $messageHistory->payloadHistory->id;
       $this->actingAs($user);
       $response = $this->delete(route('admin.message.destroy', $messageHistory));
       $this->assertDatabaseMissing('message_histories', [
           'id' => $messageHistory->id,
           'message_content' => 'test remove message history',
           'deleted_at' => NULL,
        ]);
       $response->assertRedirect(route('admin.history.show', ['history' => $payloadHistoryId]));
       $response->assertStatus(302);
   }

   /**
    * test Feature remove message history fail.
    *
    * @return void
    */
   public function testRemoveMessageHistoryFailFeature()
   {
       $messageHistory = factory(MessageHistory::class)->create(['message_content' => 'test remove message history fail']);
       $user = factory(User::class)->create(['role' => 0]);

       $this->actingAs($user);
       $response = $this->delete(route('admin.message.destroy', $messageHistory->id + 99));
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
       $response = $this->delete(route('admin.message.destroy', 1));

       $response->assertLocation('/login');
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
        $response = $this->delete(route('admin.message.destroy', $messageHistory));

        $response->assertStatus(403);
    }
}
