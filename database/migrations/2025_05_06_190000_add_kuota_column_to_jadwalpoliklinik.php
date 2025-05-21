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
        if (Schema::hasTable('jadwalpoliklinik') && !Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
            Schema::table('jadwalpoliklinik', function (Blueprint $table) {
                $table->integer('kuota')->after('jumlah')->nullable();
                
                // Update all existing records to have kuota = jumlah
                DB::statement('UPDATE jadwalpoliklinik SET kuota = jumlah WHERE kuota IS NULL');
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
        if (Schema::hasTable('jadwalpoliklinik') && Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
            Schema::table('jadwalpoliklinik', function (Blueprint $table) {
                $table->dropColumn('kuota');
            });
        }
    }
};
