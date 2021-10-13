<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plates', 7);
            $table->enum('type', ['Φ.Ι.Χ. Ανοικτό', 'Φ.Ι.Χ. Κλειστό', 'Απορριμματοφόρο', 'Πλυντήριο κάδων', 'Σάρωθρο', 'Φ.Ι.Χ Τρίκυκλο', 'Φ.Ι.Χ. Ανατρεπόμενο', 'Φ.Ι.Χ. Βυτιοφόρο', 'Φ.Ι.Χ. Τράκτορας']);
            $table->string('make')->nullable();
            $table->year('year_first_license');
            $table->float('taxable_hp')->nullable()->default(null);
            $table->float('payload');
            $table->string('payload_unit');
            $table->enum('municipality', ['ΚΕΡΚΥΡΑΙΩΝ', 'ΜΕΛΙΤΕΙΕΩΝ', 'ΘΙΝΑΛΙΩΝ', 'ΦΑΙΑΚΩΝ', 'ΕΣΠΕΡΙΩΝ', 'ΠΑΡΕΛΙΩΝ', 'ΑΧΙΛΛΕΙΩΝ', 'ΚΑΣΣΩΠΑΙΩΝ', 'ΠΑΛΑΙΟΚΑΣΤΡΙΤΩΝ', 'ΛΕΥΚΙΜΜΑΙΩΝ', 'ΑΓ. ΓΕΩΡΓΙΟΥ', 'ΚΟΡΙΣΣΙΩΝ']);
            $table->enum('unity', ['Διοίκησης', 'Ηλεκτροφωτισμού', 'Καθαριότητας']);
            $table->boolean('in_service')->default(true)->comment('When true, the vehicle can be assigned on routes');
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
        Schema::dropIfExists('vehicles');
    }
}
