<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\UserType;
use App\Repositories\Eloquents\UserRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Mockery;
use Illuminate\Support\Facades\Mail;

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
    * test Feature list user in page has no record.
    *
    * @return void
    */
   public function testListUserInNoRecordPageFeature()
   {
       $currentUser = factory(User::class)->create(['role' => UserType::ADMIN]);
       factory(User::class, 3)->create();
       $this->actingAs($currentUser);
       $response = $this->get(route('users.index', ['page' => 2]));

       $response->assertLocation(route('users.index', ['page' => 1]));
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
       $response->assertLocation('/');
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
        $response->assertRedirect('/');
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
        Mail::fake();
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

    public function testStoreUserExceptionFeature()
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
        $mock = Mockery::mock(UserRepository::class);
        $mock->shouldReceive('store')->andThrowExceptions([new QueryException('', [], new Exception)]);
        $this->app->instance(UserRepository::class, $mock);

        $response = $this->post(route('users.store'), $params);
        $response->assertSessionHas('messageFail', [
            'status' => 'Create failed',
            'message' => 'Create failed. Something went wrong',
        ]);
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
        $response->assertRedirect('/');
    }

    public function testShowDetailUser()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user = factory(User::class)->create();
        $this->actingAs($admin);
        $response = $this->get(route('users.edit', $user->id));

        $response->assertStatus(200);
    }

    public function testUpdateUserSuccessFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'Name Update',
            'email' => 'emaiupdatel@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response->assertStatus(200)->assertJsonFragment([
            'messageSuccess' => [
                'status' => 'Update success',
                'message' => 'This user successfully updated',
            ],
        ]);
    }

    public function testUpdateUserExceptionFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'Name Update',
            'email' => 'emaiupdatel@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $mock = Mockery::mock(UserRepository::class);
        $mock->shouldReceive('update')->andThrowExceptions([new QueryException('', [], new Exception)]);
        $this->app->instance(UserRepository::class, $mock);
        $response = $this->put(route('users.update', $user->id), $params);
        $response->assertStatus(200)->assertJsonFragment([
            'error' => true,
                'messageFail' => [
                    'status' => 'Update failed',
                    'message' => 'Update failed. Something went wrong',
                ],
        ]);
    }

    public function testUnauthenticateCannotUpdateUser()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'Name Update',
            'email' => 'emaiupdatel@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function testUnauthorizationCannotUpdateUser()
    {
        $user1 = factory(User::class)->create(['role' => UserType::USER]);
        $this->actingAs($user1);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => "Name Update",
            'email' => 'emaiupdatel@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response->assertStatus(302);
    }

    public function testUpdateUserRequireName()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => null,
            'email' => 'emaiupdatel@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    public function testUpdateUserNameMinLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => '123',
            'email' => 'emaiupdatel@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    public function testUpdateUserNameMaximumLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => Str::random(51),
            'email' => 'emaiupdatel@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    public function testUpdateUserRequireEmail()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'name update',
            'email' => null,
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('email');
    }

    public function testUpdateUserUniqueEmail()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user1 = factory(User::class)->create(['email' => "emailduplicate@gmail.com"]);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'name update',
            'email' => 'emailduplicate@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('email');
    }

    public function testUpdateUserEmailMaximumLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'name update',
            'email' => Str::random(201),
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('email');
    }

    public function testUpdateUserRequirePasswordMinLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'name update',
            'email' => 'emailupdate@gmail.com',
            'password' => '123',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('password');
    }

    public function testUpdateUserRequirePasswordMaximumLength()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'name update',
            'email' => 'emailupdate@gmail.com',
            'password' => Str::random(51),
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('password');
    }

    public function testUpdateUserAvatarNotImage()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);
        $user = factory(User::class)->create(
            [
                'name' => 'Name Create',
                'email' => 'email@gmail.com',
                'password' => '12345678',
                'role' => '1',
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]
        );
        $params = [
            'name' => 'name update',
            'email' => 'emailupdate@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => 'abc',
        ];
        $response = $this->put(route('users.update', $user->id), $params);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('avatar');
    }

    public function testRemoveUserFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user = factory(User::class)->create(['role' => UserType::USER, 'name' => 'name remove']);
        $this->actingAs($admin);

        $response = $this->delete(route('users.destroy', ['user' => $user]));
        $this->assertDatabaseMissing('users', ['id' => $user->id, 'name' => 'name remove', 'deleted_at' => NULL]);
        $response->assertRedirect(route('users.index'));
        $response->assertStatus(302);
    }

    public function testRemoveUserExceptionFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user = factory(User::class)->create(['role' => UserType::USER, 'name' => 'name remove']);
        $this->actingAs($admin);
        $mock = Mockery::mock(UserRepository::class);
        $mock->shouldReceive('delete')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(UserRepository::class, $mock);

        $response = $this->delete(route('users.destroy', ['user' => $user]));
        $response->assertSessionHas('messageFail', [
            'status' => 'Delete failed',
            'message' => 'Delete failed. Something went wrong',
        ]);
    }

    public function testUserSeftRemoveFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $this->actingAs($admin);

        $response = $this->delete(route('users.destroy', ['user' => $admin]));
        $response->assertSessionHas('messageFail', [
            'status' => 'Delete failed',
            'message' => 'Delete failed, Cannot delete myself',
        ]);
    }

    public function testRemoveUserAtSecondPageFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user = factory(User::class)->create(['role' => UserType::USER, 'name' => 'name remove']);
        $this->actingAs($admin);
        $response = $this->delete(route('users.destroy', ['page' => 2, 'user' => $user]));

        $this->assertDatabaseMissing('users', ['id' => $user->id, 'name' => 'name remove', 'deleted_at' => NULL]);
        $response->assertRedirect(route('users.index', ['page' => 2]));
    }

    public function testRemoveUserFail()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user = factory(User::class)->create(['role' => UserType::USER, 'name' => 'name remove fail']);
        $this->actingAs($admin);
        $user->id = 1000;

        $response = $this->delete(route('users.destroy', ['user' => $user]));
        $this->assertDatabaseMissing('users', ['id' => $user->id, 'name' => 'name remove', 'deleted_at' => NULL]);
        $this->assertDatabaseHas('users', ['name' => 'name remove fail']);
        $response->assertStatus(404);
    }

    public function testRemoveUserUnauthenticateFeature()
    {
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $user = factory(User::class)->create();

        $response = $this->delete(route('users.destroy', ['user' => $user]));
        $response->assertLocation('/');
        $response->assertStatus(302);
    }

    public function testRemoveUserUnauthorizedFeature()
    {
        $user1 = factory(User::class)->create(['role' => UserType::USER]);
        $this->actingAs($user1);
        $user = factory(User::class)->create();

        $response = $this->delete(route('users.destroy', ['user' => $user]));
        $response->assertStatus(302);
    }
}
