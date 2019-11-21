<?php

use Illuminate\Database\Seeder;
use App\Models\Webhook;

class WebhooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Webhook::class, 10)->create();
    }
}
