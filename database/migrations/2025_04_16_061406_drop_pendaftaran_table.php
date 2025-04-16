<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop any foreign keys first to avoid constraint issues
        Schema::table('pendaftaran', function (Blueprint $table) {
            try {
                $table->dropForeign(['jadwalpoliklinik_id']);
                $table->dropForeign(['id_pasien']);
            } catch (\Exception $e) {
                // It's okay if constraints don't exist
            }
        });

        Schema::dropIfExists('pendaftaran');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't recreate the table in down() as it will be recreated by the next migration
    }
};
