<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWproducersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function __construct()
    {
        \Illuminate\Support\Facades\DB::getDoctrineSchemaManager()
        ->getDatabasePlatform()->registerDoctrineTypeMapping('point', 'string');
    }

    public function up()
    {
        Schema::table('w_producers', function (Blueprint $table) {
            $table->dropUnique(['title'])->change();
            $table->dropUnique(['contact_name'])->change();
            $table->dropUnique(['contact_telephone'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('w_producers', function (Blueprint $table) {
            //
        });
    }
}
