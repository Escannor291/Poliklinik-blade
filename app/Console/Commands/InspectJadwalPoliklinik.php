<?php

namespace App\Console\Commands;

use App\Models\JadwalPoliklinik;
use Illuminate\Console\Command;
use Carbon\Carbon;

class InspectJadwalPoliklinik extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:inspect {id?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect jadwal poliklinik records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        $showAll = $this->option('all');
        
        $today = Carbon::today();
        $now = Carbon::now();
        
        $this->info("Today: {$today}");
        $this->info("Current time: {$now->format('H:i:s')}");
        
        if ($id) {
            $jadwal = JadwalPoliklinik::with('dokter.poliklinik')->find($id);
            if (!$jadwal) {
                $this->error("Jadwal with ID {$id} not found!");
                return 1;
            }
            $this->displayJadwal($jadwal);
        } else {
            $query = JadwalPoliklinik::with('dokter.poliklinik');
            
            if (!$showAll) {
                $query->whereDate('tanggal_praktek', '>=', $today);
            }
            
            $jadwals = $query->orderBy('tanggal_praktek')->get();
            
            $this->info("Found {$jadwals->count()} jadwal records");
            
            foreach ($jadwals as $jadwal) {
                $this->displayJadwal($jadwal);
                $this->line('-----------------------');
            }
        }
        
        return 0;
    }
    
    protected function displayJadwal($jadwal)
    {
        $this->info("ID: {$jadwal->id}");
        $this->info("Kode: {$jadwal->kode}");
        $this->info("Tanggal: " . $jadwal->tanggal_praktek);
        $this->info("Jam: {$jadwal->jam_mulai} - {$jadwal->jam_selesai}");
        $this->info("Dokter: {$jadwal->dokter->nama_dokter}");
        $this->info("Poliklinik: {$jadwal->dokter->poliklinik->nama_poliklinik}");
        $this->info("Jumlah/Kuota: {$jadwal->jumlah}/{$jadwal->kuota}");
    }
}
