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
        // Memperbesar panjang kolom kode_antrian dan kode_jadwalpoliklinik
        if (Schema::hasTable('antrian')) {
            Schema::table('antrian', function (Blueprint $table) {
                $table->string('kode_antrian', 255)->nullable()->change();
                $table->string('kode_jadwalpoliklinik', 255)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak perlu melakukan apa-apa saat rollback
    }
};
