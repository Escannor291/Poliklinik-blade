<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        // Matikan foreign key checks untuk memungkinkan modifikasi tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Fix jadwalpoliklinik.kode column to be VARCHAR
        if (Schema::hasTable('jadwalpoliklinik') && Schema::hasColumn('jadwalpoliklinik', 'kode')) {
            DB::statement('ALTER TABLE jadwalpoliklinik MODIFY COLUMN kode VARCHAR(255)');
        }
        
        // Fix antrian.kode_jadwalpoliklinik column to be VARCHAR
        if (Schema::hasTable('antrian') && Schema::hasColumn('antrian', 'kode_jadwalpoliklinik')) {
            DB::statement('ALTER TABLE antrian MODIFY COLUMN kode_jadwalpoliklinik VARCHAR(255)');
        }
        
        // Fix antrian.id_pasien to allow NULL or have default 0
        if (Schema::hasTable('antrian') && Schema::hasColumn('antrian', 'id_pasien')) {
            DB::statement('ALTER TABLE antrian MODIFY COLUMN id_pasien BIGINT UNSIGNED DEFAULT 0 NULL');
        }
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
