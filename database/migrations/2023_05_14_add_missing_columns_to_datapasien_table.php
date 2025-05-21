<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToDatapasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datapasien', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('datapasien', 'no_bpjs')) {
                $table->string('no_bpjs')->nullable();
            }
            if (!Schema::hasColumn('datapasien', 'scan_bpjs')) {
                $table->string('scan_bpjs')->nullable();
            }
            if (!Schema::hasColumn('datapasien', 'scan_asuransi')) {
                $table->string('scan_asuransi')->nullable();
            }
            if (!Schema::hasColumn('datapasien', 'no_kberobat')) {
                $table->string('no_kberobat')->nullable();
            }
            if (!Schema::hasColumn('datapasien', 'scan_kberobat')) {
                $table->string('scan_kberobat')->nullable();
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
            $table->dropColumn(['no_bpjs', 'scan_bpjs', 'scan_asuransi', 'no_kberobat', 'scan_kberobat']);
        });
    }
}
