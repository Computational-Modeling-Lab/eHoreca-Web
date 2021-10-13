<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_routes', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->unsignedBigInteger('route_id')->foreign('route_id')->references('id')->on('routes')->primary()->onDelete('cascade');
            $table->enum('type', ['all', 'compost', 'glass', 'recyclable', 'mixed', 'metal', 'paper', 'plastic'])->default('all');
            $table->boolean('route_completed')->default(false);
            $table->unsignedBigInteger('concluded_at_bin_id')->default(0);
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
        Schema::dropIfExists('vehicle_routes');
    }
}
