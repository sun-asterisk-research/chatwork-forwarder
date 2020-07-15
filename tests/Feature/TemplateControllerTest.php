<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Eloquents\TemplateRepository;
use Exception;
use Mockery;
use App\Enums\TemplateStatus;

class TemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowListTemplateFeature()
    {
        $templateLists = factory(Template::class, 2)->create();
        $user = $templateLists[0]->user;

        $this->actingAs($user);
        $response = $this->get('/templates');
        $response->assertStatus(200);
        $response->assertViewHas('templates');
    }

    /**
     * test user can see create template form
     *
     * @return void
     */
    public function testUserCanSeeCreateTemplateForm()
    {
        $user = factory(User::class)->make();

        $this->actingAs($user);
        $response = $this->get(route('templates.create'));

        $response->assertStatus(200)
            ->assertViewIs('templates.create');
    }

    /**
     * test user unauthorized cannot see create template form
     *
     * @return void
     */
    public function testUnauthorizedUserCannotSeeCreateTemplateForm()
    {
        $response = $this->get(route('templates.create'));

        $response->assertStatus(302)
            ->assertRedirect('/');
    }

    /**
     * test user authorized can create a new template
     *
     * @return void
     */
    public function testUserCanCreateANewTemplate()
    {
        $user = factory(User::class)->create();
        $params = [
            'name' => 'Test',
            'content' => 'Hi my name is {{name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ];

        $this->actingAs($user);
        $response = $this->post(route('templates.store'), $params);

        $response->assertStatus(200);
        $response->assertSessionHas('messageSuccess', [
            'status' => 'Create success',
            'message' => 'This template successfully created',
        ]);
        $this->assertEquals(1, Template::all()->count());
    }

    public function testCreateTemplateWithExceptionFeature()
    {
        $user = factory(User::class)->create();
        $params = [
            'name' => 'Test',
            'content' => 'Hi my name is {{name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ];
        $this->actingAs($user);

        $mock = Mockery::mock(TemplateRepository::class);
        $mock->shouldReceive('create')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(TemplateRepository::class, $mock);
        $response = $this->post(route('templates.store'), $params);
        $response->assertSessionHas('messageFail', [
            'status' => 'Create failed',
            'message' => 'Create failed. Something went wrong',
        ]);
    }

    /**
     * test Feature store template with message content not match with params
     *
     * @return void
     */
    public function testStoreTemplateInvalidContentFeature()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->post(route('templates.store'), [
            'content' => 'Hi my name is {{$abc}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'name' => 'Test',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ]);
        $errors = session('errors')->toArray();

        $response->assertStatus(302);
        $this->assertEquals($errors['content'][0], '$abc not found in params');
    }

    /**
     * test template required name
     *
     * @return void
     */
    public function testTemplateRequireName()
    {
        $user = factory(User::class)->create();
        $params = [
            'name' => null,
            'content' => 'Hi my name is {{$name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ];

        $this->actingAs($user);
        $response = $this->post(route('templates.store'), $params);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test template unique name with a user
     *
     * @return void
     */
    public function testTemplateUniqueNameWithUser()
    {
        $template = factory(Template::class)->create();
        $user = $template->user;

        $params = [
            'name' => $template->name,
            'content' => 'Hi my name is {{$name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ];

        $this->actingAs($user);
        $response = $this->post(route('templates.store'), $params);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test template name have maximum length is 50 characters
     *
     * @return void
     */
    public function testTemplateNameMaximumLength()
    {
        $user = factory(User::class)->create();
        $params = [
            'name' => 'assdkdkdkdassdkdkdkdassdkdkdkdassdkdkdkdassdkdkdkd1',
            'content' => 'Hi my name is {{$name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ];

        $this->actingAs($user);
        $response = $this->post(route('templates.store'), $params);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test template name have minimumlength is 2 characters
     *
     * @return void
     */
    public function testTemplateNameMinimumLength()
    {
        $user = factory(User::class)->create();
        $params = [
            'name' => 'a',
            'content' => 'Hi my name is {{$name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ];

        $this->actingAs($user);
        $response = $this->post(route('templates.store'), $params);

        $response->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    /**
     * test Feature remove template successfully.
     *
     * @return void
     */
    public function testRemoveTemplateFeature()
    {
        $template = factory(Template::class)->create();
        $user = $template->user;

        $this->actingAs($user);
        $response = $this->delete(route('templates.destroy', $template->id));
        $this->assertDatabaseMissing('templates', [
            'id' => $template->id,
            'deleted_at' => null,
        ]);
        $response->assertRedirect('/templates');
        $response->assertStatus(302);
    }

    public function testRemoveTemplateWithExceptionFeature()
    {
        $template = factory(Template::class)->create();
        $user = $template->user;

        $this->actingAs($user);

        $mock = Mockery::mock(TemplateRepository::class);
        $mock->shouldReceive('delete')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(TemplateRepository::class, $mock);
        $response = $this->delete(route('templates.destroy', $template->id));
        $response->assertSessionHas('messageFail', [
            'status' => 'Delete failed',
            'message' => __('message.notification.delete.fail', ['object' => 'template']),
        ]);
    }

    /**
     * test Feature show template detail success
     *
     * @return void
     */
    public function testShowTemplateDetailSuccessFeature()
    {
        $template = factory(Template::class)->create();
        $user = $template->user;

        $this->actingAs($user);

        $response = $this->get(route('templates.edit', $template->id));
        $response->assertStatus(200);
        $response->assertViewHas(['template']);
    }

    /**
     * test Feature show template detail when user not login
     *
     * @return void
     */
    public function testShowTemplateDetailUnauthorizedFeature()
    {
        $template = factory(Template::class)->create();
        $response = $this->get(route('templates.edit', $template->id));

        $response->assertLocation('/');
        $response->assertStatus(302);
    }

    /**
     * test Feature show template does not exist
     *
     * @return void
     */
    public function testShowNotExistTemplateDetailFeature()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $response = $this->get(route('templates.edit', -1));

        $response->assertStatus(404);
    }

    /**
     * test Feature template is updated successfully
     *
     * @return void
     */
    public function testUpdateTemplateSuccessFeature()
    {
        $user = factory(User::class)->create();
        $template = factory(Template::class)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);
        $response = $this->put(route('templates.update', $template->id), [
            'id' => $template->id,
            'name' => $template->name,
            'content' => 'Hi my name is {{$params.name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ]);
        $result = Template::find($template->id);

        $response->assertStatus(200);
        $response->assertSessionHas('messageSuccess', [
            'status' => 'Update success',
            'message' => 'This template successfully updated',
        ]);
        $this->assertEquals($result->content, 'Hi my name is {{$params.name}}');
    }

    public function testUpdateTemplateFailException()
    {
        $user = factory(User::class)->create();
        $template = factory(Template::class)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $mock = Mockery::mock(TemplateRepository::class);
        $mock->shouldReceive('create')->andThrowExceptions([new Exception('Exception', 100)]);
        $this->app->instance(TemplateRepository::class, $mock);

        $response = $this->put(route('templates.update', $template->id), [
            'id' => $template->id,
            'name' => 'Test',
            'content' => 'Hi my name is {{$params.name}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ]);

        $response->assertSessionHas('messageFail', [
            'status' => 'Update failed',
            'message' => 'Update failed. Something went wrong',
        ]);
    }

    /**
     * test Feature updating template when violating validation rule
     *
     * @return void
     */
    public function testUpdateTemplateFailedFeature()
    {
        $template = factory(Template::class)->create();
        $user = $template->user;

        $this->actingAs($user);
        $response = $this->put(route('templates.update', $template->id), [
            'id' => $template->id,
            'content' => '',
            'params' => '',
            'name' => 'Test',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'content' => 'Please enter content',
            'params' => 'Please enter template params',
        ]);
    }

    /**
     * test Feature updating template with invalid message content
     *
     * @return void
     */
    public function testUpdateTemplateInvalidContentFeature()
    {
        $template = factory(Template::class)->create();
        $user = $template->user;

        $this->actingAs($user);
        $response = $this->put(route('templates.update', $template->id), [
            'id' => $template->id,
            'name' => 'Test',
            'content' => 'Hi my name is {{$abc}}',
            'params' => '{"name": "rasmus", "age": "30"}',
            'status' => TemplateStatus::STATUS_PUBLIC,
        ]);
        $errors = session('errors')->toArray();

        $response->assertStatus(302);
        $this->assertEquals($errors['content'][0], '$abc not found in params');
    }

    /**
     * test Feature updating a template does not exist
     *
     * @return void
     */
    public function testUpdateTemplateNotExistFeature()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $response = $this->put(route('templates.update', -1), [
            'content' => 'New content'
        ]);

        $response->assertStatus(404);
    }

    /**
     * test user can change status template
     *
     * @return  void
     */
    public function testAuthorizedUserCanUnpublicTemplate()
    {
        $template = factory(Template::class)->create(['status' => TemplateStatus::STATUS_PUBLIC]);

        $this->actingAs($template->user);
        $response = $this->put("templates/change_status", [
            'id' => $template->id,
            'status' => TemplateStatus::STATUS_UNPUBLIC,
        ]);

        $this->assertDatabaseHas('templates', ['id' => $template->id, 'status' => TemplateStatus::STATUS_UNPUBLIC]);
        $response->assertSee('This template was updated successfully');
    }

    /**
     * test user can change status template to public
     *
     * @return  void
     */
    public function testAuthorizedUserCanChangeStatusToPublicTemplate()
    {
        $template = factory(Template::class)->create(['status' => TemplateStatus::STATUS_UNPUBLIC]);
        $this->actingAs($template->user);
        $response = $this->put("templates/change_status", [
            'id' => $template->id,
            'status' => TemplateStatus::STATUS_PUBLIC,
        ]);

        $this->assertDatabaseHas('templates', ['id' => $template->id, 'status' => TemplateStatus::STATUS_PUBLIC]);
        $response->assertSee('This template was updated successfully');
    }

    /**
     * test user change status template fail with incorrect id
     *
     * @return  void
     */
    public function testChangeStatusTemplateFailFeature()
    {
        $template = factory(Template::class)->create();
        $this->actingAs($template->user);

        $response = $this->put('templates/-1/change_status', ['status' => TemplateStatus::STATUS_PUBLIC]);

        $response->assertStatus(404);
    }

    /**
     * test user change status template fail with permission denied
     *
     * @return  void
     */
    public function testChangeStatusTemplateFailWithPermissionDeniedFeature()
    {
        $template = factory(Template::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->put("templates/change_status", ['status' => TemplateStatus::STATUS_PUBLIC]);
        $response->assertStatus(403);
    }

    /**
     * test unauthorized user cannot change status template
     *
     * @return  void
     */
    public function testUnauthorizedUserCannotChangeStatusTemplate()
    {
        $template = factory(Template::class)->create();

        $response = $this->put("templates/change_status", ['status' => TemplateStatus::STATUS_PUBLIC]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    /**
     * test admin can change status of user's template
     *
     * @return  void
     */
    public function testUserChangeStatusTemplateFailed()
    {
        $user = factory(User::class)->create();
        $template = factory(Template::class)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $mock = Mockery::mock(TemplateRepository::class);
        $mock->shouldReceive('find')->andReturn($template);
        $mock->shouldReceive('update')->andReturn(false);
        $this->app->instance(TemplateRepository::class, $mock);

        $response = $this->put("templates/change_status", ['status' => TemplateStatus::STATUS_UNPUBLIC]);
        $response->assertStatus(400);
        $response->assertSee('Updated failed. Something went wrong');
    }
}
