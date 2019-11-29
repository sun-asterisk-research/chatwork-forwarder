<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bot;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test Feature list bots.
     *
     * @return void
     */
    public function testShowListChatbotFeature()
    {
        $chatbotList = factory(Bot::class, 2)->create();
        $user = $chatbotList[0]->user;

        $this->actingAs($user);
        $response = $this->get('/bots');
        $response->assertStatus(200);
        $response->assertViewHas('bots');
    }

    /**
     * test user can see create bot form
     *
     * @return void
     */
    public function testUserCanSeeCreateBotForm()
    {
        $user = factory(User::class)->make();

        $this->actingAs($user);
        $response = $this->get(route('bots.create'));

        $response
            ->assertStatus(200)
            ->assertViewIs('bots.create');
    }

    /**
     * test user unauthorized cannot see create bot form
     *
     * @return void
     */
    public function testUnauthorizedUserCannotSeeCreateBotForm()
    {
        $user = factory(User::class)->make();

        $response = $this->get(route('bots.create'));

        $response
            ->assertStatus(302)
            ->assertRedirect('login');
    }

    /**
     * test user authorized can create a new bot
     *
     * @return void
     */
    public function testUserCanCreateANewBot()
    {
        $user = factory(User::class)->create();
        $params = [
            'name' => 'Test Bot',
            'cw_id' => '131233',
            'bot_key' => 'asdg12asd3423adasdasd23sdasdas23',
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response->assertRedirect('bots/' . Bot::first()->id . '/edit');
        $this->assertEquals(1, Bot::all()->count());
    }

    /**
     * test bot required name
     *
     * @return void
     */
    public function testBotRequireName()
    {
        $user = factory(User::class)->create();
        $params = [
            'name' => NULL,
            'cw_id' => '131233',
            'bot_key' => 'asdg12asd3423adasdasd23sdasdas23',
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test bot unique name with a user
     *
     * @return void
     */
    public function testBotUniqueNameWithUser()
    {
        $bot = factory(Bot::class)->create();
        $user = $bot->user;

        $params = [
            'name' => $bot->name,
            'cw_id' => '131233',
            'bot_key' => 'asdg12asd3423adasdasd23sdasdas23',
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test bot name have maximum length is 50 characters
     *
     * @return void
     */
    public function testBotNameMaximumLength()
    {
        $bot = factory(Bot::class)->create();
        $user = $bot->user;

        $params = [
            'name' => 'assdkdkdkdassdkdkdkdassdkdkdkdassdkdkdkdassdkdkdkd1',
            'cw_id' => '131233',
            'bot_key' => 'asdg12asd3423adasdasd23sdasdas23',
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test bot required cw_id
     *
     * @return void
     */
    public function testBotRequiredCWId()
    {
        $user = factory(User::class)->create();

        $params = [
            'name' => 'asd',
            'cw_id' => NULL,
            'bot_key' => 'asdg12asd3423adasdasd23sdasdas23',
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('cw_id');
    }

    /**
     * test bot unique cw_id with user
     *
     * @return void
     */
    public function testBotUniqueCWIdWithUser()
    {
        $bot = factory(Bot::class)->create();
        $user = $bot->user;

        $params = [
            'name' => 'asd',
            'cw_id' => $bot->cw_id,
            'bot_key' => 'asdg12asd3423adasdasd23sdasdas23',
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('cw_id');
    }

    /**
     * test bot cw_id have maximum length is 50 characters
     *
     * @return void
     */
    public function testBotCWIdMaximumLength()
    {
        $bot = factory(Bot::class)->create();
        $user = $bot->user;

        $params = [
            'name' => 'asd',
            'cw_id' => '131233234113123323411312332341131233234113123323412',
            'bot_key' => 'asdg12asd3423adasdasd23sdasdas23',
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('cw_id');
    }

    /**
     * test bot required bot_key
     *
     * @return void
     */
    public function testBotRequiredBotKey()
    {
        $user = factory(User::class)->create();

        $params = [
            'name' => 'asd',
            'cw_id' => '12321',
            'bot_key' => NULL,
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('bot_key');
    }

    /**
     * test bot unique bot_key with user
     *
     * @return void
     */
    public function testBotUniqueBotKeyWithUser()
    {
        $bot = factory(Bot::class)->create();
        $user = $bot->user;

        $params = [
            'name' => 'asd',
            'cw_id' => '12321',
            'bot_key' => $bot->bot_key,
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('bot_key');
    }

     /**
     * test bot bot_key have maximum length is 50 characters
     *
     * @return void
     */
    public function testBotKeyMaximumLength()
    {
        $bot = factory(Bot::class)->create();
        $user = $bot->user;

        $params = [
            'name' => 'asd',
            'cw_id' => '12321',
            'bot_key' => 'asdasdasdwasdasdasdwasdasdasdwasdasdasdwasdasdasdwe',
        ];

        $this->actingAs($user);
        $response = $this->post(route('bots.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('bot_key');
    }

    /**
    * test Feature remove bot successfully.
    *
    * @return void
    */
   public function testRemoveChatbotFeature()
   {
       $bot = factory(Bot::class)->create(['name' => 'test remove bot']);
       $user = $bot->user;

       $this->actingAs($user);
       $response = $this->delete(route('bots.destroy', $bot->id));
       $this->assertDatabaseMissing('bots', ['name' => 'test remove bot']);
       $response->assertRedirect('/bots');
       $response->assertStatus(302);
   }

   /**
    * test Feature remove bot fail.
    *
    * @return void
    */
   public function testRemoveChatbotFailFeature()
   {
       $bot = factory(Bot::class)->create(['name' => 'test remove bot fail']);
       $user = $bot->user;

       $this->actingAs($user);
       $response = $this->delete(route('bots.destroy', ($bot->id + 99)));
       $this->assertDatabaseHas('bots', ['name' => 'test remove bot fail']);
       $response->assertRedirect('/bots');
       $response->assertStatus(302);
   }

   /**
    * test Feature remove bot unauthorized
    *
    * @return void
    */
   public function testRemoveChatbotUnauthorizedFeature()
   {
       $response = $this->delete(route('bots.destroy', 1));

       $response->assertLocation('/login');
       $response->assertStatus(302);
   }
}
