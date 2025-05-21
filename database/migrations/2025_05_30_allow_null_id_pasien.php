<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Matikan foreign key checks untuk memungkinkan modifikasi tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        try {
            // Ubah kolom id_pasien di tabel antrian menjadi nullable atau default 0
            if (Schema::hasTable('antrian')) {
                // Coba tambahkan default value 0 untuk id_pasien
                Schema::table('antrian', function (Blueprint $table) {
                    if (Schema::hasColumn('antrian', 'id_pasien')) {
                        // Hapus foreign key terlebih dahulu jika ada
                        try {
                            if (DB::getSchemaBuilder()->getColumnListing('antrian')) {
                                $foreignKeys = DB::select("SHOW CREATE TABLE antrian");
                                $createTableScript = $foreignKeys[0]->{'Create Table'};
                                
                                if (strpos($createTableScript, 'CONSTRAINT `antrian_id_pasien_foreign`') !== false) {
                                    $table->dropForeign(['id_pasien']);
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('Error dropping foreign key: ' . $e->getMessage());
                        }
                        
                        // Ubah kolom id_pasien menjadi default 0 dan nullable
                        $table->unsignedBigInteger('id_pasien')->default(0)->nullable()->change();
                    }
                });
                
                // Jalankan ALTER TABLE langsung jika migration dengan Blueprint gagal
                try {
                    DB::statement('ALTER TABLE antrian MODIFY COLUMN id_pasien BIGINT UNSIGNED DEFAULT 0 NULL');
                } catch (\Exception $e) {
                    \Log::error('Error modifying column with direct statement: ' . $e->getMessage());
                }
                
                // Tambahkan kembali foreign key dengan opsi nullable jika diperlukan
                Schema::table('antrian', function (Blueprint $table) {
                    if (Schema::hasTable('datapasien')) {
                        $table->foreign('id_pasien')->references('id')->on('datapasien')->onDelete('set null');
                    }
                });
            }
        } catch (\Exception $e) {
            \Log::error('Migration error: ' . $e->getMessage());
        }
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak perlu implementasi rollback untuk fix ini
    }
};
