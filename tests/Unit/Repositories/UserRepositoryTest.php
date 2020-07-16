<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Repositories\Eloquents\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
$request = new \Illuminate\Http\Request();

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test get model
     *
     * @return void
     */
    public function testGetModel()
    {
        $userRepository = new UserRepository;

        $data = $userRepository->getModel();
        $this->assertEquals(User::class, $data);
    }

    /**
     * test store function model
     *
     * @return void
     */
    public function testStore()
    {
        $userRepository = new UserRepository;
        $params = [
            'name' => 'abc',
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $request = new \Illuminate\Http\Request();
        $request->replace($params);

        $result = $userRepository->store($request);
        $this->assertEquals(1, User::all()->count());
    }

    /**
     * test getAllAndSearch with search params
     *
     * @return void
     */
    public function testGetAllAndSearch()
    {
        $user1 = factory(User::class)->create(['name' => 'rasmus', 'email' => 'rasmusa@sun-asterisk.com']);
        factory(User::class)->create(['name' => 'rasmus', 'email' => 'rasmusb@sun-asterisk.com']);

        $userRepository = new UserRepository;
        $perPage = config('paginate.perPage');
        $searchParams = [
            'name' => 'rasmus',
            'email' => 'rasmusb@sun-asterisk.com',
        ];

        $result = $userRepository->getAllAndSearch($perPage, $searchParams);
        $this->assertCount(1, $result);
        $this->assertEquals($user1->name, $result[0]->name);

        $resultNotFound = $userRepository->getAllAndSearch($perPage, ['name' => 'asd']);
        $this->assertCount(0, $resultNotFound);
    }

    /**
     * test getAllAndSearch without keyword
     *
     * @return void
     */
    public function testGetAllAndSearchWithoutKeyword()
    {
        factory(User::class, 2)->create();
        $userRepository = new UserRepository;
        $perPage = config('paginate.perPage');

        $this->assertCount(2, $userRepository->getAllAndSearch($perPage, null));

        $this->assertCount(2, $userRepository->getAllAndSearch($perPage, ['name' => '', 'email' => '']));
    }

    /**
     * test update function
     *
     * @return void
     */
    public function testUpdate()
    {
        $user = factory(User::class)->create();
        $userRepository = new UserRepository;
        $params = [
            'name' => 'abc',
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $request = new \Illuminate\Http\Request();
        $request->replace($params);

        $result = $userRepository->update($user->id, $request);
        $this->assertEquals(1, User::all()->count());
    }

    /**
     * test update function when not found id
     *
     * @return void
     */
    public function testUpdateNotFoundID()
    {
        $userRepository = new UserRepository;
        $params = [
            'name' => 'abc',
            'email' => 'email@gmail.com',
            'password' => '12345678',
            'role' => '1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        $request = new \Illuminate\Http\Request();
        $request->replace($params);

        $result = $userRepository->update(-1, $request);
        $this->assertEquals(false, $result);
    }
}
