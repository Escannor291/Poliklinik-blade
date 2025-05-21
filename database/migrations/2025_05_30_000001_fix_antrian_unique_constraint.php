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
        // Matikan foreign key checks untuk memungkinkan modifikasi tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Jika tabel antrian sudah ada
        if (Schema::hasTable('antrian')) {
            // Periksa apakah ada indeks unique pada kolom kode_antrian
            $hasUniqueKey = collect(DB::select("SHOW INDEXES FROM antrian WHERE Column_name = 'kode_antrian' AND Non_unique = 0"))->count() > 0;
            
            // Jika ada indeks unique, hapus
            if ($hasUniqueKey) {
                DB::statement('ALTER TABLE antrian DROP INDEX antrian_kode_antrian_unique');
            }
            
            // Ubah kolom kode_antrian menjadi string agar bisa menyimpan format unik baru
            DB::statement('ALTER TABLE antrian MODIFY kode_antrian VARCHAR(255) NULL');
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak perlu rollback operasi perbaikan
    }
};
