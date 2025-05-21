<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\JadwalPoliklinik;
use App\Models\Dokter;
use App\Models\Poliklinik;
use App\Models\Datapasien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanPendaftaranController extends Controller
{
    public function index(Request $request)
    {
        // Initialize variables for the view
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Get all pendaftaran with relationships
        $query = Pendaftaran::with(['jadwalpoliklinik.dokter.poliklinik']);
        
        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('jadwalpoliklinik', function($q) use ($request) {
                $q->whereBetween('tanggal_praktek', [$request->start_date, $request->end_date]);
            });
        }
        
        // Filter by poliklinik if provided
        if ($request->filled('poliklinik_id') && $request->poliklinik_id != 'all') {
            $query->whereHas('jadwalpoliklinik', function($q) use ($request) {
                $q->where('poliklinik_id', $request->poliklinik_id);
            });
        }
        
        // Filter by dokter if provided
        if ($request->filled('dokter_id') && $request->dokter_id != 'all') {
            $query->whereHas('jadwalpoliklinik', function($q) use ($request) {
                $q->where('dokter_id', $request->dokter_id);
            });
        }
        
        // Get all pendaftaran
        $pendaftarans = $query->orderBy('created_at', 'desc')->get();
        
        // Count by penjamin type
        $total_pendaftaran = $pendaftarans->count();
        $total_umum = $pendaftarans->where('penjamin', 'UMUM')->count();
        $total_bpjs = $pendaftarans->where('penjamin', 'BPJS')->count();
        $total_asuransi = $pendaftarans->where('penjamin', 'Asuransi')->count();
        
        // Get all polikliniks for filter
        $polikliniks = Poliklinik::all();
        $dokters = [];
        
        if ($request->filled('poliklinik_id') && $request->poliklinik_id != 'all') {
            $dokters = Dokter::where('poliklinik_id', $request->poliklinik_id)->get();
        }
        
        return view('laporan_pendaftaran.index', compact(
            'pendaftarans', 
            'total_pendaftaran', 
            'total_umum', 
            'total_bpjs', 
            'total_asuransi', 
            'polikliniks',
            'dokters',
            'startDate',
            'endDate'
        ));
    }
    
    public function exportPdf(Request $request)
    {
        // Implement PDF generation logic here
        // For now, we'll just return a message
        return back()->with('error', 'Fitur export PDF sedang dalam pengembangan');
    }

    public function getDoktersByPoliklinik(Request $request)
    {
        $dokters = Dokter::where('poliklinik_id', $request->poliklinik_id)->get();
        return response()->json($dokters);
    }
    
    // Add edit functionality
    public function edit($id)
    {
        // Check if user is admin
        if (auth()->user()->roles !== 'admin') {
            return redirect()->route('laporan_pendaftaran.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit pendaftaran.');
        }
        
        $pendaftaran = Pendaftaran::with(['jadwalpoliklinik.dokter.poliklinik'])->findOrFail($id);
        $jadwalpolikliniks = JadwalPoliklinik::with(['dokter.poliklinik'])->get();
        
        return view('laporan_pendaftaran.edit', compact('pendaftaran', 'jadwalpolikliniks'));
    }
    
    // Add update functionality
    public function update(Request $request, $id)
    {
        // Check if user is admin
        if (auth()->user()->roles !== 'admin') {
            return redirect()->route('laporan_pendaftaran.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengupdate pendaftaran.');
        }
        
        $request->validate([
            'penjamin' => 'required|in:UMUM,BPJS,Asuransi',
            'jadwalpoliklinik_id' => 'required|exists:jadwalpoliklinik,id',
        ]);
        
        $pendaftaran = Pendaftaran::findOrFail($id);
        
        // If jadwalpoliklinik_id is changed, adjust quotas
        if ($pendaftaran->jadwalpoliklinik_id != $request->jadwalpoliklinik_id) {
            // Increase quota in the old jadwal
            $oldJadwal = JadwalPoliklinik::find($pendaftaran->jadwalpoliklinik_id);
            if ($oldJadwal) {
                if (Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
                    $oldJadwal->increment('kuota');
                } else {
                    $oldJadwal->increment('jumlah');
                }
            }
            
            // Decrease quota in the new jadwal
            $newJadwal = JadwalPoliklinik::find($request->jadwalpoliklinik_id);
            if ($newJadwal) {
                if (Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
                    $newJadwal->decrement('kuota');
                } else {
                    $newJadwal->decrement('jumlah');
                }
            }
        }
        
        // Update pendaftaran
        $pendaftaran->jadwalpoliklinik_id = $request->jadwalpoliklinik_id;
        $pendaftaran->penjamin = $request->penjamin;
        
        // Handle file upload for BPJS if a new file is provided
        if ($request->penjamin == 'BPJS' && $request->hasFile('scan_surat_rujukan')) {
            $file = $request->file('scan_surat_rujukan');
            $path = $file->store('public/surat_rujukan');
            $pendaftaran->scan_surat_rujukan = $path;
        }
        
        $pendaftaran->save();
        
        // Also update related antrian record if it exists
        $antrian = DB::table('antrian')
            ->where('jadwalpoliklinik_id', $pendaftaran->jadwalpoliklinik_id)
            ->where('id_pasien', $pendaftaran->id_pasien)
            ->first();
            
        if ($antrian) {
            $jadwal = $pendaftaran->jadwalpoliklinik;
            DB::table('antrian')
                ->where('id', $antrian->id)
                ->update([
                    'penjamin' => $request->penjamin,
                    'jadwalpoliklinik_id' => $request->jadwalpoliklinik_id,
                    'nama_dokter' => $jadwal->dokter->nama_dokter,
                    'poliklinik' => $jadwal->dokter->poliklinik->nama_poliklinik,
                    'tanggal_berobat' => $jadwal->tanggal_praktek,
                    'scan_surat_rujukan' => $pendaftaran->scan_surat_rujukan,
                ]);
        }
        
        return redirect()->route('laporan_pendaftaran.index')
            ->with('success', 'Pendaftaran berhasil diperbarui.');
    }
    
    // Add delete functionality
    public function destroy($id)
    {
        // Check if user is admin
        if (auth()->user()->roles !== 'admin') {
            return redirect()->route('laporan_pendaftaran.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus pendaftaran.');
        }
        
        $pendaftaran = Pendaftaran::findOrFail($id);
        
        // Increase quota in jadwalpoliklinik
        $jadwal = JadwalPoliklinik::find($pendaftaran->jadwalpoliklinik_id);
        if ($jadwal) {
            if (Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
                $jadwal->increment('kuota');
            } else {
                $jadwal->increment('jumlah');
            }
        }
        
        // Delete related antrian if exists
        DB::table('antrian')
            ->where('jadwalpoliklinik_id', $pendaftaran->jadwalpoliklinik_id)
            ->where('id_pasien', $pendaftaran->id_pasien)
            ->delete();
        
        // Delete pendaftaran
        $pendaftaran->delete();
        
        return redirect()->route('laporan_pendaftaran.index')
            ->with('success', 'Pendaftaran berhasil dihapus.');
    }
    
    /**
     * Generate and download PDF for a specific registration
     */
    public function downloadPdf($id)
    {
        // Get the pendaftaran with all relations
        $pendaftaran = Pendaftaran::with([
            'jadwalpoliklinik.dokter.poliklinik',
            'datapasien'
        ])->findOrFail($id);
        
        // Get antrian record if exists
        $antrian = DB::table('antrian')
            ->where('jadwalpoliklinik_id', $pendaftaran->jadwalpoliklinik_id)
            ->where('id_pasien', $pendaftaran->id_pasien)
            ->first();
            
        // Format date properly
        $tanggalPraktek = null;
        if ($pendaftaran->jadwalpoliklinik) {
            $tanggalPraktek = Carbon::parse($pendaftaran->jadwalpoliklinik->tanggal_praktek)->format('d/m/Y');
        }
        
        // Generate PDF
        $pdf = PDF::loadView('laporan_pendaftaran.detail_pdf', [
            'pendaftaran' => $pendaftaran,
            'antrian' => $antrian,
            'tanggal_praktek' => $tanggalPraktek
        ]);
        
        // Set PDF options
        $pdf->setPaper('a4', 'portrait');
        
        // Download the PDF with a proper filename
        $filename = 'pendaftaran_' . $pendaftaran->id . '_' . $pendaftaran->nama_pasien . '.pdf';
        
        return $pdf->download($filename);
    }
}
