<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWProducersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_producers', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('contact_name')->unique();
            $table->string('join_pin', 4);
            $table->string('contact_telephone', 15)->unique();
            $table->point('location');
            $table->mediumText('description')->nullable()->default(null);
            $table->json('users');
            $table->json('bins');
            $table->boolean('is_approved')->default(false);
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
        Schema::dropIfExists('w_producers');
    }
}
