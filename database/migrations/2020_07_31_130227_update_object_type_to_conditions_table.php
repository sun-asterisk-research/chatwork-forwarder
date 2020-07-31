<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateObjectTypeToConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('conditions')
            ->where('object_type', '=', '')
            ->orWhereNull('object_type')
            ->update(['object_type' => 'App\Models\Payload']);
    }
}
