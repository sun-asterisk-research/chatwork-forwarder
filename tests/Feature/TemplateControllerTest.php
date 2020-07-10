<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\TemplateStatus;
use App\Repositories\Eloquents\TemplateRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Mockery;

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
}
