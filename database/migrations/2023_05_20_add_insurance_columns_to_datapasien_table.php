<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsuranceColumnsToDatapasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datapasien', function (Blueprint $table) {
            if (!Schema::hasColumn('datapasien', 'no_bpjs')) {
                $table->string('no_bpjs')->nullable();
            }
            if (!Schema::hasColumn('datapasien', 'scan_bpjs')) {
                $table->string('scan_bpjs')->nullable();
            }
            if (!Schema::hasColumn('datapasien', 'scan_asuransi')) {
                $table->string('scan_asuransi')->nullable();
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
        Schema::table('datapasien', function (Blueprint $table) {
            if (Schema::hasColumn('datapasien', 'no_bpjs')) {
                $table->dropColumn('no_bpjs');
            }
            if (Schema::hasColumn('datapasien', 'scan_bpjs')) {
                $table->dropColumn('scan_bpjs');
            }
            if (Schema::hasColumn('datapasien', 'scan_asuransi')) {
                $table->dropColumn('scan_asuransi');
            }
        });
    }
}
