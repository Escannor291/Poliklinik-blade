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
        if (!Schema::hasTable('rating')) {
            Schema::create('rating', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dokter_id');
                $table->unsignedBigInteger('pasien_id')->nullable();
                $table->integer('rating')->default(5); // 1-5 rating scale
                $table->text('komentar')->nullable();
                $table->date('tanggal')->nullable();
                $table->timestamps();
                
                // Foreign keys
                $table->foreign('dokter_id')->references('id')->on('dokter')->onDelete('cascade');
                $table->foreign('pasien_id')->references('id')->on('datapasien')->onDelete('set null');
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
        Schema::dropIfExists('rating');
    }
};
