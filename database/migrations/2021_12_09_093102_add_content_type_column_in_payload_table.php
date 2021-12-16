<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentTypeColumnInPayloadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payloads', function (Blueprint $table) {
            $table->string('content_type')->default('text')->after('webhook_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payloads', function (Blueprint $table) {
            $table->dropColumn('content_type');
        });
    }
}
