@extends('layout.pasien')

@section('title', 'Dashboard Pasien')

@section('content')
<div class="container">
    <h1 class="mb-4">Detail Data Pribadi</h1>
    
    <div class="card"></div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('pasien.edit', $dataPasien->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit/Lengkapi Data
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1">Nama Pasien:</p>
                            <p class="font-weight-bold">{{ $dataPasien->nama_pasien ?? 'Pasien User' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">Email/Username:</p>
                            <p class="font-weight-bold">{{ $dataPasien->email ?? 'pasien' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1">No. Telepon:</p>
                            <p class="font-weight-bold">{{ $dataPasien->no_telp ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">NIK:</p>
                            <p class="font-weight-bold">{{ $dataPasien->nik ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1">Tempat Lahir:</p>
                            <p class="font-weight-bold">{{ $dataPasien->tempat_lahir ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">Tanggal Lahir:</p>
                            <p class="font-weight-bold">{{ $dataPasien->tanggal_lahir ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1">Jenis Kelamin:</p>
                            <p class="font-weight-bold">{{ $dataPasien->jenis_kelamin ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">Alamat:</p>
                            <p class="font-weight-bold">{{ $dataPasien->alamat ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1">No. Kartu Berobat:</p>
                            <p class="font-weight-bold">{{ $dataPasien->no_kberobat ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">No. Kartu BPJS:</p>
                            <p class="font-weight-bold">{{ $dataPasien->no_kbpjs ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 text-center">
                    @if(isset($user->foto_user) && $user->foto_user)
                        <img src="{{ asset('storage/foto_user/' . $user->foto_user) }}" alt="Foto User" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    @else
                        <div class="alert alert-info">
                            Foto User tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Scan KTP:</h5>
                    @if(isset($dataPasien->scan_ktp) && $dataPasien->scan_ktp)
                        <img src="{{ asset('storage/' . $dataPasien->scan_ktp) }}" alt="Scan KTP" class="img-fluid mb-3" style="max-height: 200px;">
                    @else
                        <div class="alert alert-info">
                            Gambar KTP tidak ditemukan.
                        </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <h5>Scan Kartu Berobat:</h5>
                    @if(isset($dataPasien->scan_kberobat) && $dataPasien->scan_kberobat)
                        <img src="{{ asset('storage/' . $dataPasien->scan_kberobat) }}" alt="Scan Kartu Berobat" class="img-fluid mb-3" style="max-height: 200px;">
                    @else
                        <div class="alert alert-info">
                            Gambar Kartu Berobat tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Scan BPIS:</h5>
                    @if(isset($dataPasien->scan_kbpjs) && $dataPasien->scan_kbpjs)
                        <img src="{{ asset('storage/' . $dataPasien->scan_kbpjs) }}" alt="Scan BPJS" class="img-fluid mb-3" style="max-height: 200px;">
                    @else
                        <div class="alert alert-info">
                            Gambar BPIS tidak ditemukan.
                        </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <h5>Scan Asuransi:</h5>
                    @if(isset($dataPasien->scan_kasuransi) && $dataPasien->scan_kasuransi)
                        <img src="{{ asset('storage/' . $dataPasien->scan_kasuransi) }}" alt="Scan Asuransi" class="img-fluid mb-3" style="max-height: 200px;">
                    @else
                        <div class="alert alert-info">
                            Gambar Asuransi tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection