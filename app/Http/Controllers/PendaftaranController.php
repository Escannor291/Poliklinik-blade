<?php

namespace App\Http\Controllers;
use App\Models\JadwalPoliklinik;
use Carbon\Carbon;
use App\Models\Pendaftaran;
use App\Models\Antrian;
use App\Models\dokter;
use Illuminate\Support\Facades\Auth;
use App\Models\Datapasien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // Tambahkan logging untuk debugging
        Log::info('Current date and time: ' . $today . ' ' . $now->format('H:i'));

        // Ambil jadwal untuk hari ini yang belum lewat waktu sekarang
        $jadwalHariIni = JadwalPoliklinik::with('dokter.poliklinik')
                        ->whereDate('tanggal_praktek', $today)
                        ->where(function($query) use ($now) {
                            // Tampilkan jadwal yang jam selesainya belum lewat atau jika jam selesainya null
                            $query->where('jam_selesai', '>', $now->format('H:i'))
                                  ->orWhereNull('jam_selesai');
                        })
                        ->where(function($query) {
                            // Tampilkan jadwal yang masih memiliki kuota
                            $query->where('jumlah', '>', 0)
                                  ->orWhere('kuota', '>', 0);
                        })
                        ->get();

        // Log jadwal untuk debugging
        Log::info('Jadwal hari ini count: ' . $jadwalHariIni->count());
        foreach ($jadwalHariIni as $jadwal) {
            Log::info("Jadwal ID {$jadwal->id}: Dokter {$jadwal->dokter->nama_dokter}, Jam {$jadwal->jam_mulai}-{$jadwal->jam_selesai}, Kuota {$jadwal->jumlah}");
        }

        $tomorrow = Carbon::tomorrow();
        $jadwalBesok = JadwalPoliklinik::with('dokter.poliklinik')
                     ->whereDate('tanggal_praktek', $tomorrow)
                     ->where(function($query) {
                         $query->where('jumlah', '>', 0)
                               ->orWhere('kuota', '>', 0);
                     })
                     ->get();

        // Jadwal mendatang (lebih dari besok)
        $jadwalMendatang = JadwalPoliklinik::with('dokter.poliklinik')
                         ->whereDate('tanggal_praktek', '>', $tomorrow)
                         ->where(function($query) {
                             $query->where('jumlah', '>', 0)
                                   ->orWhere('kuota', '>', 0);
                         })
                         ->orderBy('tanggal_praktek')
                         ->get();

        // Get dokter ratings
        $dokterIds = $jadwalHariIni->pluck('dokter_id')
                    ->merge($jadwalBesok->pluck('dokter_id'))
                    ->merge($jadwalMendatang->pluck('dokter_id'))
                    ->unique();

        $dokterRatings = [];

        // Periksa apakah tabel rating ada
        if (Schema::hasTable('rating')) {
            try {
                foreach ($dokterIds as $dokterId) {
                    $rating = DB::table('rating')
                            ->where('dokter_id', $dokterId)
                            ->avg('rating');

                    $dokterRatings[$dokterId] = $rating ?: 5; // Default 5 jika tidak ada rating
                }
            } catch (\Exception $e) {
                \Log::error('Error saat mengambil rating: ' . $e->getMessage());
                // Gunakan default rating
                foreach ($dokterIds as $dokterId) {
                    $dokterRatings[$dokterId] = 5;
                }
            }
        } else {
            // Jika tabel tidak ada, gunakan default rating
            foreach ($dokterIds as $dokterId) {
                $dokterRatings[$dokterId] = 5;
            }
        }

        return view('pendaftaran.index', compact('today', 'tomorrow', 'jadwalHariIni', 'jadwalBesok', 'jadwalMendatang', 'dokterRatings'));
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
        $maxRetries = 3;
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            try {
                $user = Auth::user();
                
                Log::info('Processing pendaftaran with data: ', $request->except(['_token', 'scan_surat_rujukan']));
                
                // Initial validation rules
                $validationRules = [
                    'penjamin' => 'required|in:UMUM,BPJS,Asuransi',
                    'jadwalpoliklinik_id' => 'required|exists:jadwalpoliklinik,id',
                ];
                
                // Different rules for admin/petugas vs patients
                if ($user->roles === 'admin' || $user->roles === 'petugas') {
                    $validationRules['pasien_id'] = 'required|exists:datapasien,id';
                }
                
                // Add BPJS specific validation if penjamin is BPJS
                if ($request->penjamin == 'BPJS') {
                    $validationRules['scan_surat_rujukan'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:2048';
                }
                
                $request->validate($validationRules);
                
                // Get patient data differently based on user role
                if ($user->roles === 'admin' || $user->roles === 'petugas') {
                    // Admin selects a patient
                    $datapasien = Datapasien::findOrFail($request->pasien_id);
                } else {
                    // For patients, use their own data
                    $datapasien = Datapasien::where('user_id', $user->id)->first();
                    
                    if (!$datapasien) {
                        // If patient data doesn't exist, create it
                        $datapasien = new Datapasien();
                        $datapasien->user_id = $user->id;
                        $datapasien->nama_pasien = $user->nama_user;
                        $datapasien->email = $user->username;
                        $datapasien->no_telp = $user->no_telepon ?? '';
                        $datapasien->save();
                        
                        Log::info('Created new patient data for user ID: ' . $user->id);
                    }
                    
                    // Validate insurance data for patients (admins can bypass this)
                    if ($request->penjamin == 'BPJS' && (empty($datapasien->no_bpjs) || empty($datapasien->scan_bpjs))) {
                        return back()->withErrors(['msg' => 'Data BPJS Anda belum lengkap. Mohon lengkapi data BPJS terlebih dahulu.']);
                    }
                    
                    if ($request->penjamin == 'Asuransi' && empty($datapasien->scan_asuransi)) {
                        return back()->withErrors(['msg' => 'Data Asuransi Anda belum lengkap. Mohon lengkapi data Asuransi terlebih dahulu.']);
                    }
                }
                
                $nama_pasien = $datapasien->nama_pasien;
                $id_pasien = $datapasien->id;
                $no_telp = $datapasien->no_telp;
                
                // Check jadwal and quota
                $jadwalpoliklinik = JadwalPoliklinik::findOrFail($request->jadwalpoliklinik_id);
                $kuota = Schema::hasColumn('jadwalpoliklinik', 'kuota') ? $jadwalpoliklinik->kuota : $jadwalpoliklinik->jumlah;
                
                if ($kuota <= 0) {
                    return back()->withErrors(['msg' => 'Kuota pendaftaran habis.']);
                }
                
                // Create database tables if they don't exist
                $this->createTablesIfNotExist();
                
                // Start transaction
                DB::beginTransaction();
                
                try {
                    $pendaftaran = new Pendaftaran();
                    $pendaftaran->jadwalpoliklinik_id = $request->jadwalpoliklinik_id;
                    $pendaftaran->penjamin = $request->penjamin;
                    $pendaftaran->nama_pasien = $nama_pasien;
                    $pendaftaran->id_pasien = $id_pasien;
                    
                    // Handle file upload for BPJS
                    if ($request->penjamin == 'BPJS' && $request->hasFile('scan_surat_rujukan')) {
                        $file = $request->file('scan_surat_rujukan');
                        $path = $file->store('surat_rujukan', 'public');
                        $pendaftaran->scan_surat_rujukan = $path;
                    }
                    
                    // Save pendaftaran
                    $pendaftaran->save();
                    
                    // Decrement kuota
                    if (Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
                        $jadwalpoliklinik->decrement('kuota');
                    } else {
                        $jadwalpoliklinik->decrement('jumlah');
                    }
                    
                    // Create antrian
                    $no_antrian = Antrian::where('jadwalpoliklinik_id', $jadwalpoliklinik->id)->count() + 1;
                    $kode_jadwal = (string)($jadwalpoliklinik->kode ?? "JP-{$jadwalpoliklinik->id}");
                    
                    // Prepare additional insurance data based on penjamin
                    $insuranceData = [];
                    
                    if ($request->penjamin == 'BPJS') {
                        $insuranceData = [
                            'no_bpjs' => $datapasien->no_bpjs,
                            'scan_bpjs' => $datapasien->scan_bpjs,
                            'scan_keaslian' => null
                        ];
                    } elseif ($request->penjamin == 'Asuransi') {
                        $insuranceData = [
                            'no_bpjs' => null,
                            'scan_bpjs' => null,
                            'scan_keaslian' => $datapasien->scan_asuransi
                        ];
                    } else {
                        $insuranceData = [
                            'no_bpjs' => null,
                            'scan_bpjs' => null,
                            'scan_keaslian' => null
                        ];
                    }
                    
                    $antrianData = [
                        'kode_antrian' => $no_antrian,
                        'kode_jadwalpoliklinik' => $kode_jadwal,
                        'no_antrian' => $no_antrian,
                        'nama_pasien' => $nama_pasien,
                        'no_telp' => $no_telp,
                        'jadwalpoliklinik_id' => $jadwalpoliklinik->id,
                        'id_pasien' => $id_pasien,
                        'nama_dokter' => $jadwalpoliklinik->dokter->nama_dokter,
                        'poliklinik' => $jadwalpoliklinik->dokter->poliklinik->nama_poliklinik,
                        'penjamin' => $request->penjamin,
                        'tanggal_berobat' => $jadwalpoliklinik->tanggal_praktek,
                        'tanggal_reservasi' => now()->toDateString(),
                        'user_id' => Auth::id(),
                        'scan_surat_rujukan' => $pendaftaran->scan_surat_rujukan,
                    ];
                    
                    // Merge insurance data with antrian data
                    $antrianData = array_merge($antrianData, $insuranceData);
                    
                    $antrian = Antrian::create($antrianData);
                    
                    DB::commit();
                    
                    // Redirect with appropriate route based on user role
                    if ($user->roles === 'admin' || $user->roles === 'petugas') {
                        return redirect()->route('laporan_pendaftaran.index')
                            ->with('success', 'Pendaftaran berhasil! Nomor antrian: ' . $no_antrian);
                    } else {
                        return redirect()->route('pendaftaran.history')
                            ->with('success', 'Pendaftaran berhasil! Nomor antrian Anda: ' . $no_antrian);
                    }
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Transaction failed: " . $e->getMessage());
                    throw $e;
                }
                
            } catch (\Exception $e) {
                Log::error('Pendaftaran error: ' . $e->getMessage());
                $attempt++;
                
                if ($attempt >= $maxRetries) {
                    return back()->withErrors(['msg' => 'Terjadi kesalahan: ' . $e->getMessage()]);
                }
            }
        }
        
        return back()->withErrors(['msg' => 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.']);
    }

    /**
     * Create necessary tables if they don't exist
     */
    private function createTablesIfNotExist()
    {
        // Create pendaftaran table if needed
        if (!Schema::hasTable('pendaftaran')) {
            Schema::create('pendaftaran', function ($table) {
                $table->id();
                $table->unsignedBigInteger('jadwalpoliklinik_id');
                $table->string('nama_pasien')->nullable();
                $table->unsignedBigInteger('id_pasien')->nullable();
                $table->enum('penjamin', ['UMUM', 'BPJS', 'Asuransi']);
                $table->string('scan_surat_rujukan')->nullable();
                $table->timestamps();
            });
            
            Log::info('Created pendaftaran table dynamically');
        }

        // Create antrian table if needed
        if (!Schema::hasTable('antrian')) {
            Schema::create('antrian', function ($table) {
                $table->id();
                $table->string('kode_jadwalpoliklinik', 255);
                $table->integer('kode_antrian')->nullable();
                $table->integer('no_antrian');
                $table->string('nama_pasien');
                $table->string('no_telp')->nullable();
                $table->unsignedBigInteger('jadwalpoliklinik_id');
                $table->unsignedBigInteger('id_pasien')->default(0);
                $table->string('nama_dokter');
                $table->string('poliklinik');
                $table->string('penjamin');
                $table->string('no_bpjs')->nullable();
                $table->string('scan_bpjs')->nullable();
                $table->string('scan_keaslian')->nullable();
                $table->date('tanggal_berobat');
                $table->date('tanggal_reservasi');
                $table->string('scan_surat_rujukan')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
            
            Log::info('Created antrian table dynamically');
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
     * Show form for registration with a specific schedule
     *
     * @param  int  $jadwal_id
     * @return \Illuminate\Http\Response
     */
    public function showForm($jadwal_id)
    {
        try {
            $jadwal = JadwalPoliklinik::with(['dokter.poliklinik'])->findOrFail($jadwal_id);
            $kuota = Schema::hasColumn('jadwalpoliklinik', 'kuota') ? $jadwal->kuota : $jadwal->jumlah;

            if ($kuota > 0) {
                $datapasien = null;
                if (Auth::user()->roles === 'pasien') {
                    $datapasien = Datapasien::where('user_id', Auth::id())->first();
                }
                
                return view('pendaftaran.form', compact('jadwal', 'datapasien'));
            } else {
                // Redirect with appropriate route based on user role
                if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'petugas') {
                    return redirect()->route('pendaftaran.index')
                        ->with('error', 'Kuota pendaftaran sudah penuh');
                } else {
                    return redirect()->route('pendaftaran.pasien')
                        ->with('error', 'Kuota pendaftaran sudah penuh');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error pada showForm: ' . $e->getMessage());
            
            // Redirect with appropriate route based on user role
            if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'petugas') {
                return redirect()->route('pendaftaran.index')
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            } else {
                return redirect()->route('pendaftaran.pasien')
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
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

    /**
     * Display the patient's registration history.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        try {
            $user = auth()->user();
            
            if ($user->roles === 'pasien') {
                // For patients, show only their own registrations
                $datapasien = Datapasien::where('user_id', $user->id)->first();
                
                if ($datapasien) {
                    $pendaftarans = Pendaftaran::with(['jadwalpoliklinik.dokter.poliklinik'])
                        ->where('id_pasien', $datapasien->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
                } else {
                    $pendaftarans = collect(); // Empty collection if no patient data
                }
            } else {
                // For admin/staff, show all registrations or filtered by specified criteria
                $pendaftarans = Pendaftaran::with(['jadwalpoliklinik.dokter.poliklinik', 'datapasien'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            return view('pendaftaran.history', compact('pendaftarans'));
        } 
        catch (\Exception $e) {
            \Log::error('Error in history method: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            
            return view('pendaftaran.history', [
                'pendaftarans' => collect(),
                'error' => 'Terjadi kesalahan saat mengambil data riwayat pendaftaran.'
            ]);
        }
    }

    /**
     * Display registration page specifically for patients in dashboard style
     *
     * @return \Illuminate\Http\Response
     */
    public function pasienDashboard()
    {
        $now = Carbon::now()->setTimezone('Asia/Jakarta');
        $today = Carbon::today();

        // Get schedules for today that haven't passed current time
        $jadwalHariIni = JadwalPoliklinik::with('dokter.poliklinik')
                        ->whereDate('tanggal_praktek', $today)
                        ->where(function($query) use ($now) {
                            $query->where('jam_selesai', '>', $now->format('H:i'))
                                  ->orWhereNull('jam_selesai');
                        })
                        ->where(function($query) {
                            $query->where('jumlah', '>', 0)
                                  ->orWhere('kuota', '>', 0);
                        })
                        ->get();

        $tomorrow = Carbon::tomorrow();
        $jadwalBesok = JadwalPoliklinik::with('dokter.poliklinik')
                     ->whereDate('tanggal_praktek', $tomorrow)
                     ->where(function($query) {
                         $query->where('jumlah', '>', 0)
                               ->orWhere('kuota', '>', 0);
                     })
                     ->get();

        // Future schedules (after tomorrow)
        $jadwalMendatang = JadwalPoliklinik::with('dokter.poliklinik')
                         ->whereDate('tanggal_praktek', '>', $tomorrow)
                         ->where(function($query) {
                             $query->where('jumlah', '>', 0)
                                   ->orWhere('kuota', '>', 0);
                         })
                         ->orderBy('tanggal_praktek')
                         ->get();

        // Get doctor ratings
        $dokterIds = $jadwalHariIni->pluck('dokter_id')
                    ->merge($jadwalBesok->pluck('dokter_id'))
                    ->merge($jadwalMendatang->pluck('dokter_id'))
                    ->unique();

        $dokterRatings = [];

        // Check if rating table exists
        if (Schema::hasTable('rating')) {
            try {
                foreach ($dokterIds as $dokterId) {
                    $rating = DB::table('rating')
                            ->where('dokter_id', $dokterId)
                            ->avg('rating');

                    $dokterRatings[$dokterId] = $rating ?: 5; // Default 5 if no ratings
                }
            } catch (\Exception $e) {
                \Log::error('Error retrieving ratings: ' . $e->getMessage());
                foreach ($dokterIds as $dokterId) {
                    $dokterRatings[$dokterId] = 5;
                }
            }
        } else {
            foreach ($dokterIds as $dokterId) {
                $dokterRatings[$dokterId] = 5;
            }
        }

        // Get patient data
        $datapasien = Datapasien::where('user_id', Auth::id())->first();

        // Initialize recentPendaftaran as an empty collection
        $recentPendaftaran = collect();

        // Get recent registrations if patient data exists
        if ($datapasien) {
            $recentPendaftaran = Pendaftaran::with(['jadwalpoliklinik.dokter.poliklinik'])
                ->where('id_pasien', $datapasien->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('pendaftaran.pasien_dashboard', compact(
            'today', 
            'tomorrow', 
            'jadwalHariIni', 
            'jadwalBesok', 
            'jadwalMendatang', 
            'dokterRatings', 
            'datapasien', 
            'recentPendaftaran'
        ));
    }

    /**
     * Show admin patient registration form
     */
    public function adminRegistrationForm()
    {
        // Get available patients to register
        $patients = Datapasien::orderBy('nama_pasien')->get();
        
        // Get active schedules for today and future
        $jadwals = JadwalPoliklinik::with('dokter.poliklinik')
                  ->whereDate('tanggal_praktek', '>=', now())
                  ->where(function($query) {
                      $query->where('jumlah', '>', 0)
                            ->orWhere('kuota', '>', 0);
                  })
                  ->orderBy('tanggal_praktek')
                  ->get();
                  
        return view('pendaftaran.admin_registration', compact('patients', 'jadwals'));
    }

    /**
     * Store a registration created by admin
     */
    public function storeAdminRegistration(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'pasien_id' => 'required|exists:datapasien,id',
                'jadwalpoliklinik_id' => 'required|exists:jadwalpoliklinik,id',
                'penjamin' => 'required|in:UMUM,BPJS,Asuransi',
                'scan_surat_rujukan' => 'nullable|required_if:penjamin,BPJS|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);
            
            // Get patient data
            $datapasien = Datapasien::findOrFail($request->pasien_id);
            
            // Check if jadwal is still available
            $jadwalpoliklinik = JadwalPoliklinik::findOrFail($request->jadwalpoliklinik_id);
            $kuota = Schema::hasColumn('jadwalpoliklinik', 'kuota') ? $jadwalpoliklinik->kuota : $jadwalpoliklinik->jumlah;
            
            if ($kuota <= 0) {
                return back()->withErrors(['msg' => 'Kuota pendaftaran habis.'])->withInput();
            }
            
            // Validate insurance data if needed
            if ($request->penjamin == 'BPJS' && (empty($datapasien->no_bpjs) || empty($datapasien->scan_bpjs))) {
                if (!$request->hasFile('scan_surat_rujukan')) {
                    return back()->withErrors(['msg' => 'Surat rujukan BPJS diperlukan.'])->withInput();
                }
            }
            
            // Create database tables if they don't exist
            $this->createTablesIfNotExist();
            
            // Start transaction
            DB::beginTransaction();
            
            try {
                // Create pendaftaran record
                $pendaftaran = new Pendaftaran();
                $pendaftaran->jadwalpoliklinik_id = $request->jadwalpoliklinik_id;
                $pendaftaran->penjamin = $request->penjamin;
                $pendaftaran->nama_pasien = $datapasien->nama_pasien;
                $pendaftaran->id_pasien = $datapasien->id;
                
                // Handle file upload for BPJS
                if ($request->penjamin == 'BPJS' && $request->hasFile('scan_surat_rujukan')) {
                    $file = $request->file('scan_surat_rujukan');
                    $path = $file->store('surat_rujukan', 'public');
                    $pendaftaran->scan_surat_rujukan = $path;
                }
                
                $pendaftaran->save();
                
                // Decrement kuota
                if (Schema::hasColumn('jadwalpoliklinik', 'kuota')) {
                    $jadwalpoliklinik->decrement('kuota');
                } else {
                    $jadwalpoliklinik->decrement('jumlah');
                }
                
                // Create antrian
                $no_antrian = Antrian::where('jadwalpoliklinik_id', $jadwalpoliklinik->id)->count() + 1;
                $kode_jadwal = (string)($jadwalpoliklinik->kode ?? "JP-{$jadwalpoliklinik->id}");
                
                // Prepare additional insurance data based on penjamin
                $insuranceData = [];
                
                if ($request->penjamin == 'BPJS') {
                    $insuranceData = [
                        'no_bpjs' => $datapasien->no_bpjs,
                        'scan_bpjs' => $datapasien->scan_bpjs,
                        'scan_keaslian' => null
                    ];
                } elseif ($request->penjamin == 'Asuransi') {
                    $insuranceData = [
                        'no_bpjs' => null,
                        'scan_bpjs' => null,
                        'scan_keaslian' => $datapasien->scan_asuransi
                    ];
                } else {
                    $insuranceData = [
                        'no_bpjs' => null,
                        'scan_bpjs' => null,
                        'scan_keaslian' => null
                    ];
                }
                
                $antrianData = [
                    'kode_antrian' => $no_antrian,
                    'kode_jadwalpoliklinik' => $kode_jadwal,
                    'no_antrian' => $no_antrian,
                    'nama_pasien' => $datapasien->nama_pasien,
                    'no_telp' => $datapasien->no_telp,
                    'jadwalpoliklinik_id' => $jadwalpoliklinik->id,
                    'id_pasien' => $datapasien->id,
                    'nama_dokter' => $jadwalpoliklinik->dokter->nama_dokter,
                    'poliklinik' => $jadwalpoliklinik->dokter->poliklinik->nama_poliklinik,
                    'penjamin' => $request->penjamin,
                    'tanggal_berobat' => $jadwalpoliklinik->tanggal_praktek,
                    'tanggal_reservasi' => now()->toDateString(),
                    'user_id' => Auth::id(),
                    'scan_surat_rujukan' => $pendaftaran->scan_surat_rujukan,
                ];
                
                // Merge insurance data with antrian data
                $antrianData = array_merge($antrianData, $insuranceData);
                
                $antrian = Antrian::create($antrianData);
                
                DB::commit();
                
                return redirect()->route('laporan_pendaftaran.index')
                    ->with('success', 'Pendaftaran berhasil! Pasien ' . $datapasien->nama_pasien . ' mendapat nomor antrian: ' . $no_antrian);
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Transaction failed: " . $e->getMessage());
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Admin registration error: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}