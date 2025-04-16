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
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pasien')->nullable()->constrained('datapasien');
            $table->foreignId('jadwalpoliklinik_id')->constrained('jadwalpoliklinik'); // Using foreignId which is bigint unsigned
            $table->string('nama_pasien')->nullable();
            $table->enum('penjamin', ['UMUM', 'BPJS', 'Asuransi'])->default('UMUM');
            $table->string('scan_surat_rujukan')->nullable();
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
        Schema::dropIfExists('pendaftaran');
    }
};
