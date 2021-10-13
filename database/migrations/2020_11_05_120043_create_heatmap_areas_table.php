<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeatmapAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('heatmap_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('heatmap_id')->foreign('heatmap_id')->references('id')->on('heatmaps')->onDelete('cascade');
            $table->polygon('area');
            $table->enum('degree', ['1 - none', '2 - little', '3 - medium', '4 - a lot', '5 - very much'])->comment('degree of importance: 1=very little, 5=very important')->default('1 - none');
            $table->softDeletes();
            $table->timestamps();

            // $table->foreign('heatmap_id')->references('id')->on('heatmaps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('heatmap_areas');
    }
}
