<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Nonaktifkan foreign key checks untuk memungkinkan perubahan kolom
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Ubah tipe data kolom kode_jadwalpoliklinik menjadi varchar(255)
        if (Schema::hasTable('antrian') && Schema::hasColumn('antrian', 'kode_jadwalpoliklinik')) {
            Schema::table('antrian', function (Blueprint $table) {
                $table->string('kode_jadwalpoliklinik', 255)->change();
            });
        }
        
        // Pastikan juga tabel jadwalpoliklinik memiliki kolom 'kode' yang bertipe string
        if (Schema::hasTable('jadwalpoliklinik') && Schema::hasColumn('jadwalpoliklinik', 'kode')) {
            Schema::table('jadwalpoliklinik', function (Blueprint $table) {
                $table->string('kode', 255)->change();
            });
        }
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Perubahan balik tidak disarankan untuk menghindari kehilangan data
        // Jika benar-benar diperlukan, Anda bisa mengimplementasikan di sini
    }
};
