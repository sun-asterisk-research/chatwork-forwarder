<?php
namespace Tests\Unit;
use App\Models\User;
use Tests\TestCase;
use Mockery;
use App\Services\SocialGoogleAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
class GoogleLoginServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * create user if not exist account.
     *
     * @return void
     */
    public function testNonExistAccount()
    {
        $user = Mockery::mock('Laravel\Socialite\Contracts\User');
        $user->shouldReceive('getEmail')
            ->andReturn('hungcan1997@gmail.com')
            ->shouldReceive('getName')
            ->andReturn('quanghung')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');
        $instance = new SocialGoogleAccountService();
        $instance->createOrGetUser($user);
        $this->assertDatabaseHas('users', ['email' => 'hungcan1997@gmail.com', 'avatar' => 'https://en.gravatar.com/userimage']);
    }

    /**
     * check user if exist account.
     *
     * @return void
     */
    public function testExistAccount()
    {
        factory(User::class)->create(['name' => 'quanghung97', 'email' => 'hungcan1997@gmail.com']);
        $user = Mockery::mock('Laravel\Socialite\Contracts\User');
        $user->shouldReceive('getEmail')
            ->andReturn('hungcan1997@gmail.com')
            ->shouldReceive('getName')
            ->andReturn('quanghung97')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');
        $instance = new SocialGoogleAccountService();
        $instance->createOrGetUser($user);
        $this->assertDatabaseHas('users', ['email' => 'hungcan1997@gmail.com', 'avatar' => null, 'role' => 1]);
    }
}
