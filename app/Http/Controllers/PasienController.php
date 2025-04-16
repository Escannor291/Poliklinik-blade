<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Datapasien;
use Illuminate\Support\Facades\Auth;

class PasienController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        // Apply auth middleware to all methods
        $this->middleware('auth');
    }
    
    /**
     * Display the patient dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user has the patient role
        if ($user->roles !== 'pasien') {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // Get patient data for the logged-in user
        $dataPasien = Datapasien::where('user_id', $user->id)->first();
        
        // If patient data doesn't exist, create a basic record
        if (!$dataPasien) {
            $dataPasien = new Datapasien([
                'nama_pasien' => $user->nama_user,
                'email' => $user->username,
                'no_telp' => $user->no_telepon,
                'user_id' => $user->id,
            ]);
            $dataPasien->save();
        }
        
        // Check if scan files exist in the appropriate directory
        $dataPasien->scan_ktp = $dataPasien->scan_ktp && file_exists(public_path('storage/' . $dataPasien->scan_ktp))
            ? $dataPasien->scan_ktp : null;
        $dataPasien->scan_kberobat = $dataPasien->scan_kberobat && file_exists(public_path('storage/' . $dataPasien->scan_kberobat))
            ? $dataPasien->scan_kberobat : null;
        $dataPasien->scan_kbpjs = $dataPasien->scan_kbpjs && file_exists(public_path('storage/' . $dataPasien->scan_kbpjs))
            ? $dataPasien->scan_kbpjs : null;
        $dataPasien->scan_kasuransi = $dataPasien->scan_kasuransi && file_exists(public_path('storage/' . $dataPasien->scan_kasuransi))
            ? $dataPasien->scan_kasuransi : null;
            
        return view('dashboard-pasien', compact('dataPasien', 'user'));
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
