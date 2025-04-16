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
        // First, drop the foreign key constraint if it exists
        Schema::table('pendaftaran', function (Blueprint $table) {
            // Drop the foreign key constraint if it exists
            try {
                $table->dropForeign(['jadwalpoliklinik_id']);
            } catch (Exception $e) {
                // It's okay if the constraint doesn't exist
            }
        });

        // Then modify the column to be bigint unsigned to match the id in jadwalpoliklinik table
        Schema::table('pendaftaran', function (Blueprint $table) {
            // Change the column type to bigint unsigned (same as id())
            $table->unsignedBigInteger('jadwalpoliklinik_id')->change();
        });

        // Finally, recreate the foreign key constraint
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->foreign('jadwalpoliklinik_id')->references('id')->on('jadwalpoliklinik');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropForeign(['jadwalpoliklinik_id']);
        });
    }
};
