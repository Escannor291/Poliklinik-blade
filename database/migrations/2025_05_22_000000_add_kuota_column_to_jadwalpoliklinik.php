<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddKuotaColumnToJadwalpoliklinik extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
            Schema::table('jadwalpoliklinik', function (Blueprint $table) {
                $table->integer('kuota')->after('jumlah')->default(0);
            });
            
            // Set nilai kuota sama dengan jumlah untuk jadwal yang sudah ada
            DB::statement('UPDATE jadwalpoliklinik SET kuota = jumlah');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
            Schema::table('jadwalpoliklinik', function (Blueprint $table) {
                $table->dropColumn('kuota');
            });
        }
    }
}
