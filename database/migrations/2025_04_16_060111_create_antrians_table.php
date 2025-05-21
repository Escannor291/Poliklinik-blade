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
        // Check if table exists and drop it (to fix structure)
        if (Schema::hasTable('antrian')) {
            Schema::dropIfExists('antrian');
        }

        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jadwalpoliklinik', 255); // Pastikan ini string dengan panjang memadai
            $table->integer('kode_antrian')->nullable(); // Keep this as integer as per previous change
            $table->integer('no_antrian');
            $table->string('nama_pasien');
            $table->string('no_telp')->nullable();
            $table->unsignedBigInteger('jadwalpoliklinik_id');
            $table->unsignedBigInteger('id_pasien')->nullable();
            $table->string('nama_dokter');
            $table->string('poliklinik');
            $table->string('penjamin');
            $table->string('no_bpjs')->nullable();
            $table->string('scan_bpjs')->nullable();
            $table->string('scan_keaslian')->nullable();
            $table->date('tanggal_berobat');
            $table->date('tanggal_reservasi');
            $table->string('scan_surat_rujukan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // Define foreign keys if tables exist
            if (Schema::hasTable('jadwalpoliklinik')) {
                $table->foreign('jadwalpoliklinik_id')->references('id')->on('jadwalpoliklinik')->onDelete('cascade');
            }
            if (Schema::hasTable('datapasien')) {
                $table->foreign('id_pasien')->references('id')->on('datapasien')->onDelete('set null');
            }
            if (Schema::hasTable('user')) {
                $table->foreign('user_id')->references('id')->on('user')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('antrian');
    }
};
