@extends('layout.pasien')

@section('title', 'Dashboard Pasien')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Pasien</h1>
    </div>

    <!-- Welcome Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Selamat Datang</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4>Hai, {{ Auth::user()->nama_user }}!</h4>
                    <p>Selamat datang di sistem pendaftaran pasien Fachri Hospital. Pastikan data diri Anda sudah lengkap untuk memudahkan proses pendaftaran pada layanan kesehatan kami.</p>
                    <div class="mt-4">
                        @if(isset($dataPasien) && $dataPasien)
                            <a href="{{ route('pasien.show', $dataPasien->id) }}" class="btn btn-info btn-icon-split mr-2">
                                <span class="icon text-white-50">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <span class="text">Lihat Data Diri</span>
                            </a>
                        @endif
                        <a href="{{ route('pendaftaran.pasien') }}" class="btn btn-success btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-calendar-plus"></i>
                            </span>
                            <span class="text">Pendaftaran Baru</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    @if(isset(Auth::user()->foto_user) && Auth::user()->foto_user)
                        <img src="{{ asset('storage/foto_user/' . Auth::user()->foto_user) }}" 
                             alt="Foto User" class="img-fluid rounded-circle border shadow" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('template/img/undraw_profile.svg') }}" 
                             alt="Default User" class="img-fluid rounded-circle border shadow" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Data Diri Completion Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Kelengkapan Data</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $completionPercentage = 0;
                                    if(isset($dataPasien)) {
                                        $fields = ['nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'scan_ktp'];
                                        $completedFields = 0;
                                        foreach($fields as $field) {
                                            if(!empty($dataPasien->$field)) $completedFields++;
                                        }
                                        $completionPercentage = round(($completedFields / count($fields)) * 100);
                                    }
                                @endphp
                                {{ $completionPercentage }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Janji Medis Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical History Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Riwayat Kunjungan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Aktif</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="row">
        <!-- Data Diri -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Diri</h6>
                </div>
                <div class="card-body">
                    @if(isset($dataPasien) && $dataPasien)
                        <div class="mb-3">
                            <strong>Nama:</strong> {{ $dataPasien->nama_pasien ?? Auth::user()->nama_user }}
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong> {{ $dataPasien->email ?? Auth::user()->username }}
                        </div>
                        <div class="mb-3">
                            <strong>No. Telepon:</strong> {{ $dataPasien->no_telp ?? Auth::user()->no_telepon }}
                        </div>
                        <div class="mb-3">
                            <strong>NIK:</strong> {{ $dataPasien->nik ?? 'Belum diisi' }}
                        </div>
                        <div class="mb-3">
                            <strong>Alamat:</strong> {{ $dataPasien->alamat ?? 'Belum diisi' }}
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('pasien.edit', $dataPasien->id) }}" class="btn btn-primary btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-edit"></i>
                                </span>
                                <span class="text">Edit Data</span>
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Data pasien belum tersedia. Silakan lengkapi data diri Anda.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Medical Documents -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dokumen Medis</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-id-card fa-3x mb-3 text-gray-500"></i>
                                    <h6>KTP</h6>
                                    @if(isset($dataPasien->scan_ktp) && $dataPasien->scan_ktp)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-danger">Belum Upload</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 text-gray-500"></i>
                                    <h6>Kartu Berobat</h6>
                                    @if(isset($dataPasien->scan_kberobat) && $dataPasien->scan_kberobat)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-danger">Belum Upload</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-notes-medical fa-3x mb-3 text-gray-500"></i>
                                    <h6>BPJS</h6>
                                    @if(isset($dataPasien->no_bpjs) && isset($dataPasien->scan_bpjs) && $dataPasien->no_bpjs && $dataPasien->scan_bpjs)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-danger">Belum Lengkap</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-medical fa-3x mb-3 text-gray-500"></i>
                                    <h6>Asuransi</h6>
                                    @if(isset($dataPasien->scan_asuransi) && $dataPasien->scan_asuransi)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-danger">Belum Upload</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection