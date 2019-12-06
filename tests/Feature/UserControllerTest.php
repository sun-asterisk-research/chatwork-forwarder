<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\UserType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

   /**
    * test Feature list user
    *
    * @return void
    */
   public function testListUserFeature()
   {
       $currentUser = factory(User::class)->create(['role' => UserType::ADMIN]);
       factory(User::class, 3)->create();
       $this->actingAs($currentUser);
       $response = $this->get(route('users.index'));

       $response->assertStatus(200);
       $response->assertViewHas('users');
   }

   /**
    * test Feature list user when user not login
    *
    * @return void
    */
   public function testListUserUnauthenticatedFeature()
   {
       $response = $this->get(route('users.index'));

       $response->assertStatus(302);
       $response->assertLocation('/login');
   }

   /**
    * test Feature list user when user is not an admin
    *
    * @return void
    */
    public function testListUserNotAdminFeature()
    {
        $currentUser = factory(User::class)->create(['role' => UserType::USER]);
        $this->actingAs($currentUser);
        $response = $this->get(route('users.index'));

        $response->assertStatus(302);
        $response->assertLocation('/');
    }

    /**
    * test Feature search user by name
    *
    * @return void
    */
    public function testSearchUserByNameFeature()
    {
        $currentUser = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user1 = factory(User::class)->create(['name' => 'rasmus']);
        factory(User::class)->create(['name' => 'segigs']);
        $this->actingAs($currentUser);
        $searchParams = ['search' => ['name' => 'ras']];
        $response = $this->get(route('users.index', $searchParams));
        $responseUsers = $response->getOriginalContent()->getData()['users'];

        $response->assertStatus(200);
        $this->assertCount(1, $responseUsers);
        $this->assertEquals($user1->name, $responseUsers->first()->name);
    }

    /**
    * test Feature search user by email
    *
    * @return void
    */
    public function testSearchUserByEmailFeature()
    {
        $currentUser = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user1 = factory(User::class)->create(['name' => 'rasmus father', 'email' => 'fa.rasmus@sun-asterisk.com']);
        $user2 = factory(User::class)->create(['name' => 'rasmus son', 'email' => 'so.rasmus@sun-asterisk.com']);
        $this->actingAs($currentUser);
        $searchParams = ['search' => ['email' => 'so.ras']];
        $response = $this->get(route('users.index', $searchParams));
        $responseUsers = $response->getOriginalContent()->getData()['users'];

        $response->assertStatus(200);
        $this->assertCount(1, $responseUsers);
        $this->assertEquals($user2->email, $responseUsers->first()->email);
    }

    /**
    * test Feature search user by name & email
    *
    * @return void
    */
    public function testSearchUserByNameAndEmailFeature()
    {
        $currentUser = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user1 = factory(User::class)->create(['name' => 'rasmus father', 'email' => 'so.rasmus1@sun-asterisk.com']);
        $user2 = factory(User::class)->create(['name' => 'rasmus son', 'email' => 'so.rasmus2@sun-asterisk.com']);
        $user3 = factory(User::class)->create(['name' => 'rasmus cousin', 'email' => 'so.rasmus3@sun-asterisk.com']);
        $this->actingAs($currentUser);
        $searchParams = ['search' => ['name' => 'son', 'email' => 'so.ras']];
        $response = $this->get(route('users.index', $searchParams));
        $responseUsers = $response->getOriginalContent()->getData()['users'];

        $response->assertStatus(200);
        $this->assertCount(1, $responseUsers);
        $this->assertEquals($user2->name, $responseUsers->first()->name);
        $this->assertEquals($user2->email, $responseUsers->first()->email);
    }
}
