@extends('layout.pasien')

@section('title', 'Dashboard Pasien')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Pasien</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-auto mr-4">
                    @if(Auth::user()->foto_user)
                        <img src="{{ asset('storage/foto_user/' . Auth::user()->foto_user) }}" 
                             class="img-profile rounded-circle" 
                             style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #f8f9fc;">
                    @else
                        <img src="{{ asset('template/img/undraw_profile.svg') }}" 
                             class="img-profile rounded-circle" 
                             style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #f8f9fc;">
                    @endif
                </div>
                <div class="col">
                    <h2 class="mt-2">Hai, {{ Auth::user()->nama_user }}!</h2>
                    <p class="lead">Selamat datang di sistem pendaftaran pasien Fachri Hospital. Pastikan data diri Anda sudah lengkap untuk memudahkan proses pendaftaran.</p>
                    
                    <div class="mt-3">
                        <a href="{{ Auth::user()->datapasien ? route('pasien.show', Auth::user()->datapasien->id) : route('user.profile') }}" 
                           class="btn btn-primary mr-2">
                            <i class="fas fa-user mr-1"></i> Lihat Data Diri
                        </a>
                        <a href="{{ route('pendaftaran.index') }}" class="btn btn-success">
                            <i class="fas fa-calendar-plus mr-1"></i> Pendaftaran Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row">
        <!-- Kelengkapan Data -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                KELENGKAPAN DATA</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kelengkapan_data }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Janji Medis Aktif -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                JANJI MEDIS AKTIF</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $janji_aktif }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Kunjungan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">RIWAYAT KUNJUNGAN
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $riwayat_kunjungan }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                STATUS</div>
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

    <!-- Content Row -->
    <div class="row">
        <!-- Data Diri -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Data Diri</h6>
                    <a href="{{ Auth::user()->datapasien ? route('pasien.edit', Auth::user()->datapasien->id) : route('user.profile') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit fa-sm"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    @if(Auth::user()->datapasien)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <th width="30%">Nama</th>
                                        <td>{{ Auth::user()->nama_user }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ Auth::user()->username }}</td>
                                    </tr>
                                    <tr>
                                        <th>No. Telepon</th>
                                        <td>{{ Auth::user()->no_telepon }}</td>
                                    </tr>
                                    @if(Auth::user()->datapasien->nomor_rm)
                                    <tr>
                                        <th>Nomor RM</th>
                                        <td>{{ Auth::user()->datapasien->nomor_rm }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle mr-2"></i> Anda belum melengkapi data diri. 
                            <a href="{{ route('user.profile') }}" class="alert-link">Klik disini</a> untuk melengkapi data.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Akses Cepat -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Akses Cepat</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('pendaftaran.history') }}" class="btn btn-info btn-block">
                        <i class="fas fa-clipboard-list mr-1"></i> Riwayat Pendaftaran
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
