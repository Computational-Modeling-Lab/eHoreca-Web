<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bin_id')->foreign('bin_id')->references('id')->on('bins')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->point('location');
            $table->float('location_accuracy')->comment('in meters')->default(0);
            $table->enum('issue', ['bin full', 'bin almost full', 'bin damaged', 'bin missing']);
            $table->mediumText('comment')->nullable();
            $table->json('report_photos_filenames')->nullable();
            $table->boolean('approved')->default(false);
            $table->unsignedBigInteger('w_producer_id')->foreign('w_producer_id')->references('id')->on('w_producers')->nullable()->onDelete('cascade');
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
        Schema::dropIfExists('reports');
    }
}
