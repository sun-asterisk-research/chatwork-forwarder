<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\UserType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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

    /**
     * test Feature show create view webhook.
     *
     * @return void
     */
    public function testShowCreateViewUserFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);

        $response = $this->get(route('users.create'));
        $response->assertStatus(200);
    }

    public function testUnauthenticateUserCannotSeeCreateView()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        
        $response = $this->get(route('users.create'));
        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUserNotAdminCannotSeeCreateView()
    {
        $user = factory(User::class)->create(['role' => UserType::USER]);
        $this->actingAs($user);
        $response = $this->get(route('users.create'));
        $response->assertStatus(302);
        $response->assertLocation('/');
    }

    public function testStoreUserSuccessFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => "Name Create",
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response->assertRedirect();
    }

    public function testCreateUserRequireName()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => null,
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    public function testCreateUserNameMinLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => 'abc',
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    public function testCreateUserNameMaximumLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => Str::random(51),
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    public function testCreateUserRequireEmail()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => "Name create",
            'email' => null,
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('email');
    }

    public function testCreateUserUniqueEmail()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user1 = factory(User::class)->create(['email' => "email@gamil.com"]);

        $params = [
            'name' => "Name create",
            'email' => "email@gamil.com",
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('email');
    }

    public function testCreateUserEmailMaximumLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => Str::random(201),
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    public function testCreateUserRequirePassword()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => "Name create",
            'email' => "email@gmail.com",
            'password' => null,
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('password');
    }

    public function testCreateUserRequirePasswordMinLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => "Name create",
            'email' => "email@gmail.com",
            'password' => "123456",
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('password');
    }

    public function testCreateUserRequirePasswordMaximumLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => "Name create",
            'email' => "email@gmail.com",
            'password' => Str::random(51),
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('password');
    }

    public function testCreateUserAvatarNotImage()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $params = [
            'name' => "Name create",
            'email' => "email@gmail.com",
            'password' => Str::random(51),
            'role' => '1',
            'avatar' => "fsdsF",
        ];

        $response = $this->post(route('users.store'), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('avatar');
    }

    public function UnauthenticateUserCannotCreateUser()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $params = [
            'name' => "Name Create",
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->post(route('users.store'), $params);
        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
}
