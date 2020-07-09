<?php

use Illuminate\Database\Seeder;
use App\Models\Template;

class TemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Template::class, 10)->create();
    }
}
