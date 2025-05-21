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
        if (Schema::hasTable('antrian')) {
            Schema::table('antrian', function (Blueprint $table) {
                // First drop the column if it exists
                if (Schema::hasColumn('antrian', 'kode_antrian')) {
                    $table->dropColumn('kode_antrian');
                }
                
                // Then add it back as integer
                $table->integer('kode_antrian')->nullable()->after('kode_jadwalpoliklinik');
                
                // Make sure tanggal_reservasi is date
                if (Schema::hasColumn('antrian', 'tanggal_reservasi')) {
                    $table->date('tanggal_reservasi')->change();
                }
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
        if (Schema::hasTable('antrian')) {
            Schema::table('antrian', function (Blueprint $table) {
                if (Schema::hasColumn('antrian', 'kode_antrian')) {
                    $table->string('kode_antrian')->nullable()->change();
                }
            });
        }
    }
};
