<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use Illuminate\Support\Facades\DB;

class PasienController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:pasien');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get dataPasien model for this user
        $dataPasien = \App\Models\Datapasien::where('user_id', $user->id)->first();
        
        // Get count of active appointments
        $janji_aktif = 0;
        $riwayat_kunjungan = 0;
        
        // Check if user has datapasien relationship
        if ($dataPasien) {
            try {
                // Count active appointments (future appointments)
                $janji_aktif = DB::table('pendaftaran')
                    ->join('jadwalpoliklinik', 'pendaftaran.jadwalpoliklinik_id', '=', 'jadwalpoliklinik.id')
                    ->where('pendaftaran.id_pasien', $dataPasien->id)
                    ->whereDate('jadwalpoliklinik.tanggal_praktek', '>=', now())
                    ->count();
                
                // Count past visits
                $riwayat_kunjungan = DB::table('pendaftaran')
                    ->join('jadwalpoliklinik', 'pendaftaran.jadwalpoliklinik_id', '=', 'jadwalpoliklinik.id')
                    ->where('pendaftaran.id_pasien', $dataPasien->id)
                    ->whereDate('jadwalpoliklinik.tanggal_praktek', '<', now())
                    ->count();
            } catch (\Exception $e) {
                // Log the error but continue with default values
                \Illuminate\Support\Facades\Log::error('Error fetching pendaftaran data: ' . $e->getMessage());
            }
        }
        
        // If dataPasien exists, calculate data completeness percentage
        $completionPercentage = 0;
        if ($dataPasien) {
            $fields = ['nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'scan_ktp'];
            $completedFields = 0;
            foreach ($fields as $field) {
                if (!empty($dataPasien->$field)) $completedFields++;
            }
            $completionPercentage = round(($completedFields / count($fields)) * 100);
        }
        
        // Return view with data
        return view('dashboard-pasien', compact('dataPasien', 'janji_aktif', 'riwayat_kunjungan', 'completionPercentage'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $dataPasien = Datapasien::findOrFail($id);
            
            // Determine which layout to use based on user role
            if (Auth::user()->roles === 'pasien') {
                return view('pasien.show', compact('dataPasien'));
            } else {
                // Admin or petugas is viewing - use the correct admin view
                return view('datapasien.show', compact('dataPasien'));
            }
        } catch (\Exception $e) {
            $redirect = Auth::user()->roles === 'pasien' ? 'dashboard' : 'pasien.index';
            return redirect()->route($redirect)->with('error', 'Data pasien tidak ditemukan.');
        }
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
