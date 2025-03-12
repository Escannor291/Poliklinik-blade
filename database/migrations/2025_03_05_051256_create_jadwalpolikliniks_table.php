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
        // Periksa dulu apakah tabel sudah ada
        if (!Schema::hasTable('jadwalpoliklinik')) {
            Schema::create('jadwalpoliklinik', function (Blueprint $table) {
                $table->id();
                $table->string('kode')->unique();
                
                // Pastikan tabel dokter dan poliklinik sudah ada
                if (Schema::hasTable('dokter')) {
                    $table->foreignId('dokter_id')->constrained('dokter');
                } else {
                    $table->unsignedBigInteger('dokter_id');
                }
                
                if (Schema::hasTable('poliklinik')) {
                    $table->foreignId('poliklinik_id')->constrained('poliklinik');
                } else {
                    $table->unsignedBigInteger('poliklinik_id');
                }
                
                $table->date('tanggal_praktek');
                $table->time('jam_mulai');
                $table->time('jam_selesai');
                $table->integer('jumlah');
                $table->timestamps();
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
        // Perbaiki nama tabel agar konsisten dengan up()
        Schema::dropIfExists('jadwalpoliklinik');
    }
};