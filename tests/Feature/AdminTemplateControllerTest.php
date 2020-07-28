<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Eloquents\TemplateRepository;
use App\Enums\UserType;
use Exception;
use Mockery;
use App\Enums\TemplateStatus;

class AdminTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowListTemplateFeature()
    {
        $templateLists = factory(Template::class, 2)->create();
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($admin);
        $response = $this->get('/admin/templates');
        $response->assertStatus(200);
        $response->assertViewHas('templates');
    }

    public function testSearchTemplate()
    {
        $templateLists = factory(Template::class, 2)->create(['name' => 'template name']);
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);
        $searchParams = ['search' => ['name' => 'template name']];

        $this->actingAs($admin);
        $response = $this->get(route('admin.template.index', $searchParams));
        $response->assertStatus(200);
        $response->assertViewHas('templates');
    }

    public function testRemoveTemplateFeature()
    {
        $template = factory(Template::class)->create();
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($admin);
        $response = $this->delete(route('admin.template.destroy', $template->id));
        $this->assertDatabaseMissing('templates', [
            'id' => $template->id,
            'deleted_at' => null,
        ]);
        $response->assertRedirect('/admin/templates');
        $response->assertStatus(302);
    }

    public function testRemoveTemplateWithExceptionFeature()
    {
        $template = factory(Template::class)->create();
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($admin);

        $mock = Mockery::mock(TemplateRepository::class);
        $mock->shouldReceive('delete')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(TemplateRepository::class, $mock);
        $response = $this->delete(route('admin.template.destroy', $template->id));
        $response->assertSessionHas('messageFail', [
            'status' => 'Delete failed',
            'message' => __('message.notification.delete.fail', ['object' => 'template']),
        ]);
    }

    public function testAdminCanChangeStatus()
    {
        $template = factory(Template::class)->create(['status' => TemplateStatus::STATUS_REVIEWING]);
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($admin);

        $response = $this->put("/admin/templates/" . $template->id . "/change_status", [
            'status' => TemplateStatus::STATUS_PUBLIC,
        ]);

        $this->assertDatabaseHas('templates', ['id' => $template->id, 'status' => TemplateStatus::STATUS_PUBLIC]);
        $response->assertSee('This template was updated status successfully');
    }

    public function testUserChangeStatusTemplateFailed()
    {
        $template = factory(Template::class)->create(['status' => TemplateStatus::STATUS_PUBLIC]);
        $admin = factory(User::class)->create(['role' => UserType::ADMIN]);

        $this->actingAs($admin);
        $response = $this->put("/admin/templates/" . $template->id . "/change_status", [
            'status' => TemplateStatus::STATUS_PRIVATE,
        ]);

        $response->assertStatus(400);
        $response->assertSee('Updated failed. Something went wrong');
    }
}
