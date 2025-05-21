<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBpjsColumnsToDatapasienTable extends Migration
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
            $table->dropColumn(['no_bpjs', 'scan_bpjs']);
        });
    }
}
