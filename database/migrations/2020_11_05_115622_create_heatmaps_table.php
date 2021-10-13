<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeatmapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('heatmaps', function (Blueprint $table) {
            $table->id();
            $table->mediumText('title');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('valid_from')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('month-day');
            $table->timestamp('valid_to')->comment('month-day')->nullable()->default(null);
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
        Schema::dropIfExists('heatmaps');
    }
}
