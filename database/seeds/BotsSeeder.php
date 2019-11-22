<?php

use Illuminate\Database\Seeder;
use App\Models\Bot;

class BotsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Bot::class, 10)->create();
    }
}
