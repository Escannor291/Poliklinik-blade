<?php

namespace App\Http\Controllers;
use App\Models\JadwalPoliklinik;
use Carbon\Carbon;
use App\Models\Pendaftaran;
use App\Models\Antrian;
use App\Models\dokter;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use App\Models\Datapasien;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = Carbon::today();
        $now = Carbon::now()->setTimezone('Asia/Jakarta');
    
        // Ambil jadwal untuk hari ini yang belum lewat waktu sekarang
        $jadwalHariIni = JadwalPoliklinik::with('dokter.ratings')
                        ->whereDate('tanggal_praktek', $today)
                        ->where('jam_selesai', '>', $now->format('H:i'))
                        ->get();
    
        $tomorrow = Carbon::tomorrow();
        $jadwalBesok = JadwalPoliklinik::with('dokter.ratings')
                     ->whereDate('tanggal_praktek', $tomorrow)
                     ->get();
    
        return view('pendaftaran.index', compact('today', 'tomorrow', 'jadwalHariIni', 'jadwalBesok'));  //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->roles == 'admin' || $user->roles == 'petugas') {
                $request->validate([
                    'nama_pasien' => 'required|string|max:255',
                    'penjamin' => 'required',
                    'no_telp' => 'nullable|string|max:15',
                ]);
            
                $nama_pasien = $request->nama_pasien;
                $id_pasien = null;
                $no_telp = $request->no_telp;
            } else {
                $datapasien = Datapasien::where('user_id', $user->id)->first();
            
                if (!$datapasien) {
                    return back()->withErrors(['msg' => 'Data pasien tidak ditemukan.']);
                }
                
                $requiredFields = [
                    'nik', 'nama_pasien', 'email', 'no_telp',
                    'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
                    'alamat', 'scan_ktp', 'no_bpjs/asuransi', 'scan_bpjs/asuransi'
                ];
            
                foreach ($requiredFields as $field) {
                    if (empty($datapasien->$field)) {
                        return back()->withErrors(['msg' => 'Data diri pasien belum lengkap, harap lengkapi semua data sebelum mendaftar.']);
                    }
                }
            
                $request->validate([
                    'penjamin' => 'required',
                    'scan_surat_rujukan' => 'required_if:penjamin,BPJS|file|mimes:jpeg,png,pdf',
                ]);
            
                if ($request->penjamin == 'BPJS' && (empty($datapasien->no_bpjs) || empty($datapasien->scan_bpjs))) {
                    return back()->withErrors(['msg' => 'Data BPJS belum lengkap, harap lengkapi data BPJS terlebih dahulu!']);
                }
            
                if ($request->penjamin == 'Asuransi' && empty($datapasien->scan_keaslian)) {
                    return back()->withErrors(['msg' => 'Data Asuransi belum lengkap, harap lengkapi data Asuransi terlebih dahulu!']);
                }
            
                $nama_pasien = $datapasien->nama_pasien;
                $id_pasien = $datapasien->id;
                $no_telp = $datapasien->no_telp;
            }
            
            // Verify the jadwal poliklinik exists
            $jadwalpoliklinik = JadwalPoliklinik::findOrFail($request->jadwalpoliklinik_id);

            // Check the quota
            if ($jadwalpoliklinik->kouta <= 0) {
                return back()->withErrors(['msg' => 'Kuota pendaftaran habis']);
            }

            // Create pendaftaran record
            $pendaftaran = new Pendaftaran();
            $pendaftaran->jadwalpoliklinik_id = $request->jadwalpoliklinik_id;
            $pendaftaran->penjamin = $request->penjamin;
            $pendaftaran->nama_pasien = $nama_pasien;
            $pendaftaran->id_pasien = $id_pasien;
            
            $path = null;
            if (($user->roles == 'pasien' || $user->roles == 'petugas') && $request->hasFile('scan_surat_rujukan')) {
                $file = $request->file('scan_surat_rujukan');
                $path = $file->store('public/surat_rujukan');
                $pendaftaran->scan_surat_rujukan = $path;
            }
            
            // Save pendaftaran first
            $pendaftaran->save();
            
            // Decrement kouta (not jumlah)
            $jadwalpoliklinik->decrement('kouta');
            
            // Create antrian record
            $no_antrian = Antrian::where('jadwalpoliklinik_id', $jadwalpoliklinik->id)->count() + 1;
            $kode_antrian = $jadwalpoliklinik->poliklinik_id . $jadwalpoliklinik->dokter_id . $jadwalpoliklinik->id . $pendaftaran->id;

            $antrian = Antrian::create([
                'antrian' => $kode_antrian,
                'kode_jadwalpoliklinik' => $jadwalpoliklinik->kode_jadwalpoliklinik,
                'no_antrian' => $no_antrian,
                'nama_pasien' => $nama_pasien,
                'no_telp' => $no_telp,
                'jadwalpoliklinik_id' => $jadwalpoliklinik->id,
                'id_pasien' => $id_pasien,
                'nama_dokter' => $jadwalpoliklinik->dokter->nama_dokter,
                'poliklinik' => $jadwalpoliklinik->poliklinik->nama_poliklinik,
                'penjamin' => $request->penjamin,
                'no_bpjs' => ($request->penjamin == 'BPJS' && isset($datapasien)) ? $datapasien->no_bpjs : null,
                'scan_bpjs' => ($request->penjamin == 'BPJS' && isset($datapasien)) ? $datapasien->scan_bpjs : null,
                'scan_keaslian' => ($request->penjamin == 'Asuransi' && isset($datapasien)) ? $datapasien->scan_keaslian : null,
                'tanggal_berobat' => $jadwalpoliklinik->tanggal_praktek,
                'tanggal_reservasi' => now(),
                'user_id' => Auth::id(),
                'scan_surat_rujukan' => $path,
            ]);

            return redirect()->route('pendaftaran.index')->with('success', 'Pendaftaran berhasil!');
            
        } catch (\Exception $e) {
            \Log::error('Pendaftaran error: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
