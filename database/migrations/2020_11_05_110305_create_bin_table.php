<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->point('location');
            $table->float('capacity');
            $table->string('capacity_unit');
            $table->enum('type', ['compost', 'glass', 'recyclable', 'mixed', 'metal', 'paper', 'plastic'])->default('mixed');
            $table->string('description')->nullable();
            $table->boolean('isPublic')->default(true);
            $table->integer('quantity')->comment('quantity of such bins in this location')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bins');
    }
}
