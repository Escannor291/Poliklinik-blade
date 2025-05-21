<?php

namespace App\Http\Controllers;

use App\Models\Datapasien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DatapasienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all patient data
        $dataPasien = Datapasien::latest()->get();
        return view('datapasien.index', compact('dataPasien'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Remove user selection - no longer linking to users
        return view('datapasien.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'nama_pasien' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'no_telp' => 'nullable|string|max:15',
            'nik' => 'nullable|string|max:16',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'scan_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Create new patient data
            $datapasien = new Datapasien();
            // The user_id is now nullable, so we don't need to provide it
            // $datapasien->user_id = null; // This line is not needed as NULL is the default for nullable fields
            $datapasien->nama_pasien = $validatedData['nama_pasien'];
            $datapasien->email = $validatedData['email'] ?? null;
            $datapasien->no_telp = $validatedData['no_telp'] ?? null;
            $datapasien->nik = $validatedData['nik'] ?? null;
            $datapasien->tempat_lahir = $validatedData['tempat_lahir'] ?? null;
            $datapasien->tanggal_lahir = $validatedData['tanggal_lahir'] ?? null;
            $datapasien->jenis_kelamin = $validatedData['jenis_kelamin'] ?? null;
            $datapasien->alamat = $validatedData['alamat'] ?? null;

            // Handle KTP file upload
            if ($request->hasFile('scan_ktp')) {
                $path = $request->file('scan_ktp')->store('ktp', 'public');
                $datapasien->scan_ktp = $path;
            }

            $datapasien->save();

            return redirect()->route('pasien.index')
                ->with('success', 'Data pasien berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Error creating patient: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
        try {
            $dataPasien = Datapasien::findOrFail($id);
            
            // Use the correct layout based on user role
            $view = Auth::user()->roles === 'pasien' ? 'pasien.show' : 'datapasien.show';
            
            return view($view, compact('dataPasien'));
        } catch (\Exception $e) {
            return redirect()->route('pasien.index')->with('error', 'Data pasien tidak ditemukan.');
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
        try {
            // Get patient data
            $dataPasien = Datapasien::findOrFail($id);
            
            // If patient, can only edit their own record
            if (Auth::user()->roles === 'pasien') {
                $dataPasien = Datapasien::where('user_id', Auth::id())->firstOrFail();
            }
            
            return view('pasien.update', compact('dataPasien'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        try {
            // Validate inputs
            $validatedData = $request->validate([
                'nama_pasien' => 'required|string|max:255',
                'no_telp' => 'nullable|string|max:15',
                'nik' => 'nullable|string|max:16',
                'tempat_lahir' => 'nullable|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
                'alamat' => 'nullable|string|max:255',
                'no_kberobat' => 'nullable|string|max:20',
                'scan_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'scan_kberobat' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Find the datapasien by ID
            $datapasien = Datapasien::findOrFail($id);
            
            // Update basic fields
            $datapasien->nama_pasien = $validatedData['nama_pasien'];
            $datapasien->no_telp = $validatedData['no_telp'];
            $datapasien->nik = $validatedData['nik'];
            $datapasien->tempat_lahir = $validatedData['tempat_lahir'];
            $datapasien->tanggal_lahir = $validatedData['tanggal_lahir'];
            $datapasien->jenis_kelamin = $validatedData['jenis_kelamin'];
            $datapasien->alamat = $validatedData['alamat'];
            $datapasien->no_kberobat = $validatedData['no_kberobat'];

            // Handle KTP file upload
            if ($request->hasFile('scan_ktp')) {
                // Delete old file if exists
                if ($datapasien->scan_ktp) {
                    Storage::disk('public')->delete($datapasien->scan_ktp);
                }
                
                // Store new file
                $ktpPath = $request->file('scan_ktp')->store('ktp', 'public');
                $datapasien->scan_ktp = $ktpPath;
            }

            // Handle Kartu Berobat file upload
            if ($request->hasFile('scan_kberobat')) {
                // Delete old file if exists
                if ($datapasien->scan_kberobat) {
                    Storage::disk('public')->delete($datapasien->scan_kberobat);
                }
                
                // Store new file
                $berobatPath = $request->file('scan_kberobat')->store('kartu_berobat', 'public');
                $datapasien->scan_kberobat = $berobatPath;
            }

            // Save the changes
            $datapasien->save();
            
            // Log success
            Log::info('Datapasien updated successfully', ['id' => $id]);

            // Redirect with success message
            if (auth()->user()->roles == 'pasien') {
                return redirect()->route('dashboard-pasien')
                    ->with('success', 'Data pasien berhasil diperbarui');
            } else {
                return redirect()->route('pasien.index')
                    ->with('success', 'Data pasien berhasil diperbarui');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Datapasien not found', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Data pasien tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error updating datapasien', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update insurance information for a patient.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateInsurance(Request $request, $id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Starting insurance update', [
                'id' => $id,
                'insurance_type' => $request->insurance_type
            ]);

            // Validate the incoming request
            $validationRules = [
                'insurance_type' => 'required|in:bpjs,asuransi',
            ];
            
            // Add specific rules based on insurance type
            if ($request->insurance_type == 'bpjs') {
                $validationRules['no_bpjs'] = 'required|string|max:20';
                if ($request->hasFile('scan_bpjs')) {
                    $validationRules['scan_bpjs'] = 'file|mimes:jpeg,png,jpg,pdf|max:2048';
                }
            } else if ($request->insurance_type == 'asuransi') {
                if ($request->hasFile('scan_asuransi')) {
                    $validationRules['scan_asuransi'] = 'file|mimes:jpeg,png,jpg,pdf|max:2048';
                }
            }
            
            $request->validate($validationRules);
            
            \Illuminate\Support\Facades\Log::info('Validation passed');

            // Create missing columns if needed
            $this->ensureInsuranceColumnsExist();
            \Illuminate\Support\Facades\Log::info('Checked and ensured columns exist');

            // Find the datapasien by ID
            $datapasien = \App\Models\Datapasien::findOrFail($id);
            \Illuminate\Support\Facades\Log::info('Found patient data', ['patient_id' => $datapasien->id]);
            
            // Update data based on insurance type
            if ($request->insurance_type == 'bpjs') {
                // Update no_bpjs
                $datapasien->no_bpjs = $request->no_bpjs;
                \Illuminate\Support\Facades\Log::info('Updated BPJS number', ['no_bpjs' => $request->no_bpjs]);
                
                // Handle file upload
                if ($request->hasFile('scan_bpjs')) {
                    \Illuminate\Support\Facades\Log::info('Processing BPJS scan upload');
                    
                    // Delete old file if exists
                    if ($datapasien->scan_bpjs && \Illuminate\Support\Facades\Storage::disk('public')->exists($datapasien->scan_bpjs)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($datapasien->scan_bpjs);
                        \Illuminate\Support\Facades\Log::info('Deleted old BPJS scan');
                    }
                    
                    // Store new file
                    $file = $request->file('scan_bpjs');
                    $path = $file->store('bpjs', 'public');
                    $datapasien->scan_bpjs = $path;
                    \Illuminate\Support\Facades\Log::info('Stored new BPJS scan', ['path' => $path]);
                }
            } else if ($request->insurance_type == 'asuransi') {
                // Handle file upload
                if ($request->hasFile('scan_asuransi')) {
                    \Illuminate\Support\Facades\Log::info('Processing insurance scan upload');
                    
                    // Delete old file if exists
                    if ($datapasien->scan_asuransi && \Illuminate\Support\Facades\Storage::disk('public')->exists($datapasien->scan_asuransi)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($datapasien->scan_asuransi);
                        \Illuminate\Support\Facades\Log::info('Deleted old insurance scan');
                    }
                    
                    // Store new file
                    $file = $request->file('scan_asuransi');
                    $path = $file->store('asuransi', 'public');
                    $datapasien->scan_asuransi = $path;
                    \Illuminate\Support\Facades\Log::info('Stored new insurance scan', ['path' => $path]);
                }
            }
            
            // Save the changes
            $datapasien->save();
            \Illuminate\Support\Facades\Log::info('Insurance data saved successfully');
            
            return redirect()->back()->with('success', 'Data asuransi berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation error', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating insurance', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Ensure that required insurance columns exist in the database
     */
    private function ensureInsuranceColumnsExist()
    {
        $schema = \Illuminate\Support\Facades\Schema::connection($this->getConnectionName());
        $tableName = 'datapasien';
        
        if (!$schema->hasTable($tableName)) {
            \Illuminate\Support\Facades\Log::error('Table datapasien does not exist');
            throw new \Exception('Table datapasien does not exist');
        }
        
        // Use DB statements to add columns if they don't exist
        $columnsToCheck = ['no_bpjs', 'scan_bpjs', 'scan_asuransi'];
        
        foreach ($columnsToCheck as $column) {
            if (!$schema->hasColumn($tableName, $column)) {
                \Illuminate\Support\Facades\Log::info("Adding missing column {$column} to datapasien table");
                
                // Use raw query because Schema::table might not work properly in a controller
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE {$tableName} ADD COLUMN {$column} VARCHAR(255) NULL");
            }
        }
    }

    /**
     * Get the database connection name
     */
    private function getConnectionName()
    {
        return config('database.default');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $datapasien = Datapasien::findOrFail($id);
            
            // Delete associated files
            if ($datapasien->scan_ktp) {
                Storage::disk('public')->delete($datapasien->scan_ktp);
            }
            if ($datapasien->scan_kberobat) {
                Storage::disk('public')->delete($datapasien->scan_kberobat);
            }
            if ($datapasien->scan_bpjs) {
                Storage::disk('public')->delete($datapasien->scan_bpjs);
            }
            if ($datapasien->scan_asuransi) {
                Storage::disk('public')->delete($datapasien->scan_asuransi);
            }
            
            // Delete the record
            $datapasien->delete();
            
            return redirect()->route('pasien.index')
                ->with('success', 'Data pasien berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pasien.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}