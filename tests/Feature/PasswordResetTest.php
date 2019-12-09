<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\MailResetPasswordNotification;

class PasswordResetTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    const ROUTE_PASSWORD_EMAIL = 'password.email';
    const ROUTE_PASSWORD_REQUEST = 'password.request';
    const ROUTE_PASSWORD_RESET = 'password.reset';
    const ROUTE_PASSWORD_UPDATE = 'password.update';

    const USER_ORIGINAL_PASSWORD = 'secret';

    /**
     * Testing showing the password reset request page.
     */
    public function testShowPasswordResetRequestPage()
    {
        $this
            ->get(route(self::ROUTE_PASSWORD_REQUEST))
            ->assertSuccessful()
            ->assertSee('Reset Password')
            ->assertSee('E-Mail Address')
            ->assertSee('Send Password Reset Link');
    }

    /**
     * Testing submitting the password reset request with an invalid
     * email address.
     */
    public function testSubmitPasswordResetRequestInvalidEmail()
    {
        $this->followingRedirects()
            ->from(route(self::ROUTE_PASSWORD_REQUEST))
            ->post(route(self::ROUTE_PASSWORD_EMAIL), [
                'email' => str_random(),
            ])
            ->assertSuccessful()
            ->assertSee(__('validation.email', [
                'attribute' => 'email',
            ]));
    }

    /**
     * Testing submitting the password reset request with an email
     * address not in the database.
     */
    public function testSubmitPasswordResetRequestEmailNotFound()
    {
        $this->followingRedirects()
            ->from(route(self::ROUTE_PASSWORD_REQUEST))
            ->post(route(self::ROUTE_PASSWORD_EMAIL), [
                'email' => $this->faker->unique()->safeEmail,
            ])
            ->assertSuccessful()
            ->assertSee(e(__('passwords.user')));
    }

    /**
     * Testing submitting a password reset request.
     */
    public function testSubmitPasswordResetRequest()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $this
            ->followingRedirects()
            ->from(route(self::ROUTE_PASSWORD_REQUEST))
            ->post(route(self::ROUTE_PASSWORD_EMAIL), [
                'email' => $user->email,
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.sent'));

        Notification::assertSentTo($user, MailResetPasswordNotification::class);
    }

    /**
     * Testing showing the reset password page.
     */
    public function testShowPasswordResetPage()
    {
        $user = factory(User::class)->create();

        $token = Password::broker()->createToken($user);

        $this
            ->get(route(self::ROUTE_PASSWORD_RESET, [
                'token' => $token,
            ]))
            ->assertSuccessful()
            ->assertSee('Reset Password')
            ->assertSee('E-Mail Address')
            ->assertSee('Password')
            ->assertSee('Confirm Password');
    }

    /**
     * Testing submitting the password reset page with an invalid
     * email address.
     */
    public function testSubmitPasswordResetInvalidEmail()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD),
        ]);

        $token = Password::broker()->createToken($user);

        $password = str_random();

        $this
            ->followingRedirects()
            ->from(route(self::ROUTE_PASSWORD_RESET, [
                'token' => $token,
            ]))
            ->post(route(self::ROUTE_PASSWORD_UPDATE), [
                'token' => $token,
                'email' => str_random(),
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertSuccessful()
            ->assertSee(__('validation.email', [
                'attribute' => 'email',
            ]));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertTrue(Hash::check(self::USER_ORIGINAL_PASSWORD,
            $user->password));
    }

    /**
     * Testing submitting the password reset page with an email
     * address not in the database.
     */
    public function testSubmitPasswordResetEmailNotFound()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD),
        ]);

        $token = Password::broker()->createToken($user);

        $password = str_random();

        $this
            ->followingRedirects()
            ->from(route(self::ROUTE_PASSWORD_RESET, [
                'token' => $token,
            ]))
            ->post(route(self::ROUTE_PASSWORD_UPDATE), [
                'token' => $token,
                'email' => $this->faker->unique()->safeEmail,
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertSuccessful()
            ->assertSee(e(__('passwords.user')));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertTrue(Hash::check(self::USER_ORIGINAL_PASSWORD,
            $user->password));
    }

    /**
     * Testing submitting the password reset page with a password
     * that doesn't match the password confirmation.
     */
    public function testSubmitPasswordResetPasswordMismatch()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD),
        ]);

        $token = Password::broker()->createToken($user);

        $password = str_random();
        $password_confirmation = str_random();

        $this
            ->followingRedirects()
            ->from(route(self::ROUTE_PASSWORD_RESET, [
                'token' => $token,
            ]))
            ->post(route(self::ROUTE_PASSWORD_UPDATE), [
                'token' => $token,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password_confirmation,
            ])
            ->assertSuccessful()
            ->assertSee(__('validation.confirmed', [
                'attribute' => 'password',
            ]));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertTrue(Hash::check(self::USER_ORIGINAL_PASSWORD,
            $user->password));
    }

    /**
     * Testing submitting the password reset page.
     */
    public function testSubmitPasswordReset()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD),
        ]);

        $token = Password::broker()->createToken($user);

        $password = str_random();

        $this
            ->followingRedirects()
            ->from(route(self::ROUTE_PASSWORD_RESET, [
                'token' => $token,
            ]))
            ->post(route(self::ROUTE_PASSWORD_UPDATE), [
                'token' => $token,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertSuccessful();

        $user->refresh();

        $this->assertFalse(Hash::check(self::USER_ORIGINAL_PASSWORD,
            $user->password));

        $this->assertTrue(Hash::check($password, $user->password));
    }
}
