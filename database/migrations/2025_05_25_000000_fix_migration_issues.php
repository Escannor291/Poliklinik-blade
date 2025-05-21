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
        // Jalankan perintah SQL langsung untuk memperbaiki masalah
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');
        
        // 1. Perbarui tipe data kode_jadwalpoliklinik di antrian jika belum
        if (Schema::hasTable('antrian') && Schema::hasColumn('antrian', 'kode_jadwalpoliklinik')) {
            DB::unprepared('ALTER TABLE antrian MODIFY COLUMN kode_jadwalpoliklinik VARCHAR(255) NULL');
        }
        
        // 2. Perbarui tipe data kode di jadwalpoliklinik jika perlu
        if (Schema::hasTable('jadwalpoliklinik') && Schema::hasColumn('jadwalpoliklinik', 'kode')) {
            DB::unprepared('ALTER TABLE jadwalpoliklinik MODIFY COLUMN kode VARCHAR(255) NULL');
        }
        
        // 3. Hapus unique constraint pada kode_antrian jika ada
        $hasUniqueKey = collect(DB::select("SHOW INDEXES FROM antrian WHERE Column_name = 'kode_antrian' AND Non_unique = 0"))->count() > 0;
        if ($hasUniqueKey) {
            DB::statement('ALTER TABLE antrian DROP INDEX antrian_kode_antrian_unique');
        }
        
        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak ada operasi rollback yang diperlukan
    }
};