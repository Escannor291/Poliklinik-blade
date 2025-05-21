@extends('layout.petugas')

@section('title', 'Dashboard Petugas')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Petugas</h1>
        <a href="{{ route('laporan_pendaftaran.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Lihat Laporan
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Pendaftaran Hari Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pendaftaran Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Pendaftaran::whereDate('created_at', \Carbon\Carbon::today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokter Praktek Hari Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Dokter Praktek</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\jadwalpoliklinik::whereDate('tanggal_praktek', \Carbon\Carbon::today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-md fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Pasien Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pasien
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Datapasien::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Poliklinik Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Poliklinik Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\poliklinik::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hospital fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendaftaran Terbaru -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pendaftaran Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Pasien</th>
                            <th>Poliklinik</th>
                            <th>Dokter</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pendaftarans = \App\Models\Pendaftaran::with(['jadwalpoliklinik.dokter.poliklinik'])
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        
                        @forelse($pendaftarans as $pendaftaran)
                        <tr>
                            <td>{{ $pendaftaran->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $pendaftaran->nama_pasien }}</td>
                            <td>
                                @if($pendaftaran->jadwalpoliklinik && $pendaftaran->jadwalpoliklinik->dokter && $pendaftaran->jadwalpoliklinik->dokter->poliklinik)
                                    {{ $pendaftaran->jadwalpoliklinik->dokter->poliklinik->nama_poliklinik }}
                                @else
                                    <span class="text-danger">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($pendaftaran->jadwalpoliklinik && $pendaftaran->jadwalpoliklinik->dokter)
                                    {{ $pendaftaran->jadwalpoliklinik->dokter->nama_dokter }}
                                @else
                                    <span class="text-danger">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($pendaftaran->jadwalpoliklinik)
                                    {{ \Carbon\Carbon::parse($pendaftaran->jadwalpoliklinik->tanggal_praktek)->format('d/m/Y') }}
                                    <br>
                                    {{ substr($pendaftaran->jadwalpoliklinik->jam_mulai, 0, 5) }} - {{ substr($pendaftaran->jadwalpoliklinik->jam_selesai, 0, 5) }}
                                @else
                                    <span class="text-danger">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $appointmentDate = $pendaftaran->jadwalpoliklinik->tanggal_praktek ?? null;
                                    
                                    if (!$appointmentDate) {
                                        $status = 'Tidak Tersedia';
                                        $statusClass = 'danger';
                                    } elseif (\Carbon\Carbon::parse($appointmentDate)->lt($today)) {
                                        $status = 'Selesai';
                                        $statusClass = 'secondary';
                                    } elseif (\Carbon\Carbon::parse($appointmentDate)->eq($today)) {
                                        $status = 'Hari Ini';
                                        $statusClass = 'primary';
                                    } else {
                                        $status = 'Akan Datang';
                                        $statusClass = 'warning';
                                    }
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ $status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('laporan_pendaftaran.download_pdf', $pendaftaran->id) }}" class="btn btn-sm btn-danger" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data pendaftaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('laporan_pendaftaran.index') }}" class="btn btn-primary">
                    Lihat Semua Data Pendaftaran
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('pendaftaran.index') }}" class="btn btn-primary btn-icon-split btn-lg">
                                <span class="icon text-white-50">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                                <span class="text">Pendaftaran Baru</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('jadwalpoliklinik.index') }}" class="btn btn-info btn-icon-split btn-lg">
                                <span class="icon text-white-50">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <span class="text">Jadwal Poliklinik</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('dokter.index') }}" class="btn btn-success btn-icon-split btn-lg">
                                <span class="icon text-white-50">
                                    <i class="fas fa-user-md"></i>
                                </span>
                                <span class="text">Data Dokter</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('pasien.index') }}" class="btn btn-warning btn-icon-split btn-lg">
                                <span class="icon text-white-50">
                                    <i class="fas fa-users"></i>
                                </span>
                                <span class="text">Data Pasien</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[0, "desc"]],
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "zeroRecords": "Tidak ada data yang cocok",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });
</script>
@endpush