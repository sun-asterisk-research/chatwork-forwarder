<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Template;

class TemplateTest extends TestCase
{
    /**
     * test contains valid fillable properties
     *
     * @return void
     */
    public function testContainsValidFillableProperties()
    {
        $fillable = [
            'user_id',
            'name',
            'content_type',
            'content',
            'params',
            'status',
        ];

        $model = new Template();

        $this->assertEquals($fillable, $model->getFillable());
    }

    /**
     * test using softDeletes
     *
     * @return void
     */
    public function testUsingSoftDeleted()
    {
        $template = new Template();

        $this->assertContains('deleted_at', $template->getDates());
    }

    /**
     * test user related
     *
     * @return void
     */
    public function testUserRelate()
    {
        $template = new Template();

        $this->assertInstanceOf(BelongsTo::class, $template->user());
    }
}
