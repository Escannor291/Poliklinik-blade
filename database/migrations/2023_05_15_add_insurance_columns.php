<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsuranceColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns to datapasien table if they don't exist
        if (Schema::hasTable('datapasien')) {
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is commented out for safety, as removing columns could cause data loss
        // Schema::table('datapasien', function (Blueprint $table) {
        //     $table->dropColumn(['no_bpjs', 'scan_bpjs', 'scan_asuransi']);
        // });
    }
}
