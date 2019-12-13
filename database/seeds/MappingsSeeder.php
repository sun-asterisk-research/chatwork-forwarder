<?php

use Illuminate\Database\Seeder;
use App\Models\Mapping;

class MappingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Mapping::class, 10)->create();
    }
}
