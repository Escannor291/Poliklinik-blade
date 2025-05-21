@extends('layout.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total Patients Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pasien</div>
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

        <!-- Total Doctors Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Dokter</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Dokter::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-md fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrations Today Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pendaftaran Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Pendaftaran::whereDate('created_at', \Carbon\Carbon::today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Polyclinic Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Poliklinik</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Poliklinik::count() }}
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

    <!-- Content Row -->
    <div class="row">

        <!-- Registrations Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Pendaftaran Pasien (7 Hari Terakhir)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opsi Grafik:</div>
                            <a class="dropdown-item" href="#">7 Hari Terakhir</a>
                            <a class="dropdown-item" href="#">30 Hari Terakhir</a>
                            <a class="dropdown-item" href="#">Tahun Ini</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="registrationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Poliklinik</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opsi Tampilan:</div>
                            <a class="dropdown-item" href="#">Kunjungan Poliklinik</a>
                            <a class="dropdown-item" href="#">Distribusi Dokter</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="polyclinicDistribution"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Umum
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Gigi
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Anak
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Lainnya
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Registrations and Active Doctors -->
    <div class="row">

        <!-- Latest Registrations -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pendaftaran Terbaru</h6>
                    <a href="{{ route('laporan_pendaftaran.index') }}" class="btn btn-sm btn-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Pasien</th>
                                    <th>Poliklinik</th>
                                    <th>Dokter</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentRegistrations = \App\Models\Pendaftaran::with(['jadwalpoliklinik.dokter.poliklinik'])
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp
                                
                                @forelse($recentRegistrations as $pendaftaran)
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data pendaftaran terbaru</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Active Doctors -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dokter Praktek Hari Ini</h6>
                </div>
                <div class="card-body">
                    @php
                        $activeDoctors = \App\Models\JadwalPoliklinik::with('dokter.poliklinik')
                            ->whereDate('tanggal_praktek', \Carbon\Carbon::today())
                            ->get();
                    @endphp

                    @if($activeDoctors->count() > 0)
                        @foreach($activeDoctors as $jadwal)
                            @if($jadwal->dokter)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <div class="mr-3">
                                    @if($jadwal->dokter->foto)
                                        <img class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" 
                                            src="{{ asset('storage/' . $jadwal->dokter->foto) }}" alt="Foto Dokter">
                                    @else
                                        <img class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" 
                                            src="{{ asset('template/img/doctor.jpg') }}" alt="Foto Dokter">
                                    @endif
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $jadwal->dokter->nama_dokter }}</div>
                                    <div class="small text-gray-600">{{ $jadwal->dokter->poliklinik->nama_poliklinik }}</div>
                                    <div class="small text-gray-600">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </div>
                                </div>
                                <div class="ml-auto">
                                    <span class="badge badge-success">
                                        {{ isset($jadwal->kuota) ? $jadwal->kuota : $jadwal->jumlah }} kuota
                                    </span>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <div class="mb-3">
                                <i class="fas fa-user-md fa-4x text-gray-300"></i>
                            </div>
                            <p class="text-gray-600">Tidak ada dokter yang praktek hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Cards -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Akses Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 py-2 text-center">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="fas fa-user-plus fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-gray-800">Tambah Pasien</h5>
                                    <p class="text-gray-600">Daftarkan pasien baru ke sistem</p>
                                    <a href="{{ route('pasien.create') }}" class="btn btn-primary btn-sm">Tambah</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 py-2 text-center">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="fas fa-stethoscope fa-3x text-success"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-gray-800">Tambah Dokter</h5>
                                    <p class="text-gray-600">Tambahkan dokter baru ke dalam sistem</p>
                                    <a href="{{ route('dokter.create') }}" class="btn btn-success btn-sm">Tambah</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 py-2 text-center">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-plus fa-3x text-info"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-gray-800">Jadwal Poliklinik</h5>
                                    <p class="text-gray-600">Atur jadwal praktek dokter</p>
                                    <a href="{{ route('jadwalpoliklinik.create') }}" class="btn btn-info btn-sm">Tambah</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 py-2 text-center">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="fas fa-file-medical fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-gray-800">Laporan</h5>
                                    <p class="text-gray-600">Lihat laporan pendaftaran pasien</p>
                                    <a href="{{ route('laporan_pendaftaran.index') }}" class="btn btn-warning btn-sm">Lihat</a>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data for Registration Chart (Last 7 days)
    const registrationData = {
        labels: [
            @for($i = 6; $i >= 0; $i--)
                '{{ \Carbon\Carbon::now()->subDays($i)->format("d/m") }}',
            @endfor
        ],
        datasets: [{
            label: 'Jumlah Pendaftaran',
            data: [
                @for($i = 6; $i >= 0; $i--)
                    {{ \App\Models\Pendaftaran::whereDate('created_at', \Carbon\Carbon::now()->subDays($i))->count() }},
                @endfor
            ],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            fill: true,
            pointBackgroundColor: '#4e73df',
            tension: 0.3
        }]
    };

    // Data for Polyclinic Distribution Chart
    const polyclinicData = {
        labels: [
            @php
                try {
                    // Use lowercase model name to match your actual class name
                    $polyclinics = \App\Models\poliklinik::all()->take(4);
                    
                    foreach($polyclinics as $polyclinic) {
                        echo "'" . $polyclinic->nama_poliklinik . "',";
                    }
                } catch (\Exception $e) {
                    \Log::error('Error in dashboard chart: ' . $e->getMessage());
                    echo "'Umum','Gigi','Anak','Lainnya'";
                }
            @endphp
        ],
        datasets: [{
            data: [
                @php
                try {
                    foreach($polyclinics as $polyclinic) {
                        echo "10,"; // Use a default value or calculate based on your data
                    }
                } catch (\Exception $e) {
                    echo "10,15,8,12"; // Default values if error occurs
                }
                @endphp
            ],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
        }]
    };

    // Initialize Registration Chart
    document.addEventListener('DOMContentLoaded', function() {
        // Registration Chart
        const registrationCtx = document.getElementById('registrationsChart').getContext('2d');
        new Chart(registrationCtx, {
            type: 'line',
            data: registrationData,
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Polyclinic Distribution Chart
        const polyclinicCtx = document.getElementById('polyclinicDistribution').getContext('2d');
        new Chart(polyclinicCtx, {
            type: 'doughnut',
            data: polyclinicData,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
