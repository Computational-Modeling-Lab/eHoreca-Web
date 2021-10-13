<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('email')->unique();
            $table->string('username')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'public', 'w_producer', 'w_producer_employee', 'driver'])->default('public');
            $table->string('details')->nullable()->default(null);
            $table->string('token', 512)->nullable()->default(null);
            $table->timestamp('tokenexpiration')->nullable()->default(null);
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
        Schema::dropIfExists('users');
    }
}
