<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\JadwalPoliklinik;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class JadwalPoliklinikController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk mengambil data jadwal poliklinik
        $jadwalpoliklinik = JadwalPoliklinik::orderBy('created_at', 'desc')->get();
    
        // Ambil parameter dari permintaan GET
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
    
        // Inisialisasi query untuk semua data jadwal poliklinik
        $query = JadwalPoliklinik::query();

        // Filter berdasarkan rentang tanggal jika parameter ada
        if ($start_date && $end_date) {
            $query->whereBetween('tanggal_praktek', [$start_date, $end_date]);
        }
    
        // Ambil data jadwal poliklinik sesuai dengan query yang dibuat
        $jadwalpoliklinik = $query->orderBy('tanggal_praktek', 'asc')->get();
    
        return view('jadwalpoliklinik.index', compact('jadwalpoliklinik'));
    }
    

    public function create()
{
    $dokter = Dokter::all();
    return view('jadwalpoliklinik.create', compact('dokter'));
}


public function add(Request $request)
{
    try {
        \Log::info('Memproses tambah jadwal poliklinik', $request->all());

        $validatedData = $request->validate([
            'dokter_id' => 'required|exists:dokter,id',
            'tanggal_praktek' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'jumlah' => 'required|integer|min:1',
        ]);

        $dokter = Dokter::findOrFail($request->dokter_id);

        $jadwalpoliklinik = new JadwalPoliklinik();
        $jadwalpoliklinik->kode = 'JP-' . Str::random(8);
        $jadwalpoliklinik->dokter_id = $dokter->id;
        $jadwalpoliklinik->poliklinik_id = $dokter->poliklinik_id;
        $jadwalpoliklinik->tanggal_praktek = $request->tanggal_praktek;
        $jadwalpoliklinik->jam_mulai = $request->jam_mulai;
        $jadwalpoliklinik->jam_selesai = $request->jam_selesai;
        $jadwalpoliklinik->jumlah = $request->jumlah;
        
        // Tambahkan logging sebelum save
        \Log::info('Data jadwal akan disimpan', $jadwalpoliklinik->toArray());
        
        $jadwalpoliklinik->save();

        return redirect()->route('jadwalpoliklinik.index')
            ->with('success', 'Jadwal poliklinik berhasil ditambahkan');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Log validation errors
        \Log::error('Validation Error: ' . json_encode($e->errors()));
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        \Log::error('Tambah Jadwal Poliklinik Error: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage());
    }
}

    public function edit($id)
    {
        try {
            $jadwalpoliklinik = JadwalPoliklinik::with('dokter')->findOrFail($id);
            $dokter = Dokter::all();
            return view('jadwalpoliklinik.update', compact('jadwalpoliklinik', 'dokter'));
        } catch (\Exception $e) {
            Log::error('Edit Jadwal Poliklinik Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data jadwal');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokter,id',
            'tanggal_praktek' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i:s',
            'jam_selesai' => 'required|date_format:H:i:s|after:jam_mulai',
            'jumlah' => 'required|integer|min:1',
        ]);
    
        $jadwalpoliklinik = JadwalPoliklinik::findOrFail($id);
        $jadwalpoliklinik->dokter_id = $request->dokter_id;
        $jadwalpoliklinik->poliklinik_id = Dokter::find($request->dokter_id)->poliklinik_id;
        $jadwalpoliklinik->tanggal_praktek = $request->tanggal_praktek;
        $jadwalpoliklinik->jam_mulai = $request->jam_mulai;
        $jadwalpoliklinik->jam_selesai = $request->jam_selesai;
        $jadwalpoliklinik->jumlah = $request->jumlah;
        $jadwalpoliklinik->save();
    
        return redirect()->route('jadwalpoliklinik.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        try {
            $jadwalpoliklinik = JadwalPoliklinik::findOrFail($id);
            $jadwalpoliklinik->delete();

            return redirect()->route('jadwalpoliklinik.index')
                ->with('success', 'Jadwal poliklinik berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Hapus Jadwal Poliklinik Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
}