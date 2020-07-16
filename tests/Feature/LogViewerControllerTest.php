<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\UserType;

class LogViewerControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test access log with role admin.
     *
     * @return void
     */
    public function testAccessLogRoleAdmin()
    {
        $user = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($user);
        $response = $this->get('/logs');
        $response->assertStatus(200);
        $response->assertViewHas('logs');
    }

    /**
     * test access log with role user.
     *
     * @return void
     */
    public function testAccessLogRoleUser()
    {
        $user = factory(User::class)->create(['role' => UserType::USER]);

        $this->actingAs($user);
        $response = $this->get('/logs');
        $response->assertStatus(302);
        $response->assertLocation('/');
    }

    /**
     * test access log when user not login.
     *
     * @return void
     */
    public function testAccessLogUserNotLogin()
    {
        $user = factory(User::class)->create(['role' => UserType::ADMIN]);

        $response = $this->get('/logs');
        $response->assertStatus(302);
        $response->assertLocation('/');
    }
}
