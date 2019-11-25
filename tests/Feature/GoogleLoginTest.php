<?php

namespace Tests\Feature;

use Mockery;
use Socialite;
use Tests\TestCase;
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
}
