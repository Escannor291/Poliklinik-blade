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
        // Matikan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Hapus tabel langsung tanpa mencoba menghapus foreign keys satu per satu
        Schema::dropIfExists('pendaftaran');
        
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
        // Recreate the table if needed in down()
        if (!Schema::hasTable('pendaftaran')) {
            Schema::create('pendaftaran', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_pasien')->nullable();
                $table->unsignedBigInteger('jadwalpoliklinik_id');
                $table->string('nama_pasien')->nullable();
                $table->enum('penjamin', ['UMUM', 'BPJS', 'Asuransi']);
                $table->string('scan_surat_rujukan')->nullable();
                $table->timestamps();
            });
        }
    }
};