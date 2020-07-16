<?php

namespace Tests\Feature;

use Mockery;
use Socialite;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use App\Services\SocialGoogleAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Auth\SocialAuthGoogleController;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * redirect google login feature
     *
     * @return void
     */
    public function testRedirectGoogleFeature()
    {
        $providerMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');

        $providerMock->shouldReceive('redirect')->andReturn(new RedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);
        //Check that the user is redirected to the Social Platform Login Page
        $loginResponse = $this->call('GET', '/redirect');
        $loginResponse->assertStatus(302);
    }

    /**
     * login with role is user
     *
     * @return void
     */
    public function testCallbackGoogleFeatureWithRoleIsUser()
    {
        $user = Mockery::mock('Laravel\Socialite\Two\User');
        $user
            ->shouldReceive('getName')
            ->andReturn(str_random(10))
            ->shouldReceive('getEmail')
            ->andReturn(str_random(10) . '@gmail.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');

        $providerMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $providerMock->shouldReceive('user')->andReturn($user);

        Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);
        $loginResponse = $this->get('/callback');

        $loginResponse->assertStatus(302);
        $loginResponse->assertRedirect('/dashboard');
    }

    /**
     * login with role is admin
     *
     * @return void
     */
    public function testCallbackGoogleFeatureWithRoleIsAdmin()
    {
        factory(User::class)->create(['email' => 'test@sun-asterisk.com', 'role' => 0]);
        $mockUser = Mockery::mock('Laravel\Socialite\Two\User');
        $mockUser
            ->shouldReceive('getEmail')
            ->andReturn('test@sun-asterisk.com');

        $providerMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $providerMock->shouldReceive('user')->andReturn($mockUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);
        $loginResponse = $this->get('/callback');

        $loginResponse->assertStatus(302);
        $loginResponse->assertRedirect('/admin/dashboard');
    }
}
