@extends('layout.pasien')

@section('title', 'Pendaftaran Layanan Kesehatan')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pendaftaran Layanan Kesehatan</h1>
        <!-- Removed the "Riwayat Pendaftaran" button that was here -->
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Patient Data Status -->
    @if(!$datapasien || empty($datapasien->nik) || empty($datapasien->tanggal_lahir) || empty($datapasien->jenis_kelamin) || empty($datapasien->alamat))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Perhatian!</strong> Data pribadi Anda belum lengkap. Silakan lengkapi data diri Anda terlebih dahulu.
        <a href="{{ $datapasien ? route('pasien.edit', $datapasien->id) : '#' }}" class="alert-link">Lengkapi data sekarang</a>.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    
    <!-- Insurance Data Status -->
    @if($datapasien)
        @if(empty($datapasien->no_bpjs) || empty($datapasien->scan_bpjs))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            Data BPJS Anda belum lengkap. Jika ingin mendaftar dengan BPJS, silakan lengkapi data BPJS Anda.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        
        @if(empty($datapasien->scan_keaslian) && empty($datapasien->scan_asuransi))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            Data Asuransi Anda belum lengkap. Jika ingin mendaftar dengan Asuransi, silakan lengkapi data Asuransi Anda.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    @endif

    <!-- Registration Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Jadwal Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwalHariIni->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Jadwal Besok</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwalBesok->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jadwal Mendatang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwalMendatang->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Riwayat Pendaftaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($recentPendaftaran) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Nav Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Jadwal Poliklinik</h6>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="jadwalTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="today-tab" data-toggle="tab" href="#hari-ini" role="tab">
                        <i class="fas fa-calendar-day mr-1"></i> Hari Ini
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tomorrow-tab" data-toggle="tab" href="#tomorrow" role="tab">
                        <i class="fas fa-calendar-check mr-1"></i> Besok
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="future-tab" data-toggle="tab" href="#future" role="tab">
                        <i class="fas fa-calendar-alt mr-1"></i> Mendatang
                    </a>
                </li>
            </ul>

            <div class="tab-content mt-3" id="jadwalTabContent">
                <!-- Jadwal Hari Ini -->
                <div class="tab-pane fade show active" id="hari-ini" role="tabpanel">
                    <div class="row">
                        @if($jadwalHariIni->count() > 0)
                            @foreach($jadwalHariIni as $jadwal)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100">
                                    <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                                        <h6 class="m-0 font-weight-bold">{{ $jadwal->dokter->poliklinik->nama_poliklinik }}</h6>
                                        <span class="badge badge-light">{{ \Carbon\Carbon::parse($jadwal->tanggal_praktek)->format('d M Y') }}</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-auto mr-3">
                                                @if(isset($jadwal->dokter->foto_dokter) && $jadwal->dokter->foto_dokter)
                                                    <img src="{{ asset('storage/foto_dokter/' . $jadwal->dokter->foto_dokter) }}" class="img-fluid rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('template/img/doctor.jpg') }}" class="img-fluid rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                                                @endif
                                            </div>
                                            <div class="col">
                                                <h5 class="font-weight-bold">{{ $jadwal->dokter->nama_dokter }}</h5>
                                                
                                                <div class="mb-1">
                                                    @php
                                                        $rating = isset($dokterRatings[$jadwal->dokter_id]) ? $dokterRatings[$jadwal->dokter_id] : 5;
                                                        $fullStars = floor($rating);
                                                        $halfStar = $rating - $fullStars >= 0.5;
                                                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                                    @endphp
                                                    
                                                    @for($i=0; $i < $fullStars; $i++)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @endfor
                                                    
                                                    @if($halfStar)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    @endif
                                                    
                                                    @for($i=0; $i < $emptyStars; $i++)
                                                        <i class="far fa-star text-warning"></i>
                                                    @endfor
                                                </div>
                                                
                                                <p class="mb-0"><i class="fas fa-clock mr-1"></i> {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</p>
                                                <p class="mb-0">
                                                    Kuota: 
                                                    <span class="font-weight-bold">
                                                        @if(isset($jadwal->kuota))
                                                            {{ $jadwal->kuota }}
                                                        @else
                                                            {{ $jadwal->jumlah }}
                                                        @endif
                                                        /{{ $jadwal->jumlah }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="{{ route('pendaftaran.show', $jadwal->id) }}" class="btn btn-primary btn-block">
                                                <i class="fas fa-clipboard-list fa-sm mr-1"></i> Daftar Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-1"></i> Tidak ada jadwal praktek tersedia untuk hari ini.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Jadwal Besok -->
                <div class="tab-pane fade" id="tomorrow" role="tabpanel">
                    <div class="row mt-3">
                        @forelse($jadwalBesok as $jadwal)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between bg-success text-white">
                                    <h6 class="m-0 font-weight-bold">{{ $jadwal->dokter->poliklinik->nama_poliklinik }}</h6>
                                    <span class="badge badge-light">{{ \Carbon\Carbon::parse($jadwal->tanggal_praktek)->format('d M Y') }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto mr-3">
                                            @if(isset($jadwal->dokter->foto_dokter) && $jadwal->dokter->foto_dokter)
                                                <img src="{{ asset('storage/foto_dokter/' . $jadwal->dokter->foto_dokter) }}" class="img-fluid rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('template/img/doctor.jpg') }}" class="img-fluid rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            @endif
                                        </div>
                                        <div class="col">
                                            <h5 class="font-weight-bold">{{ $jadwal->dokter->nama_dokter }}</h5>
                                            
                                            <!-- Rating Stars -->
                                            <div class="mb-1">
                                                @php
                                                    $rating = isset($dokterRatings[$jadwal->dokter_id]) ? $dokterRatings[$jadwal->dokter_id] : 5;
                                                    $fullStars = floor($rating);
                                                    $halfStar = $rating - $fullStars >= 0.5;
                                                @endphp
                                                
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $fullStars)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @elseif($i == $fullStars + 1 && $halfStar)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            
                                            <p class="mb-1"><i class="fas fa-clock text-gray-500 mr-1"></i> {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</p>
                                            <p class="mb-1"><i class="fas fa-user-plus text-gray-500 mr-1"></i> Kuota: 
                                                <span class="font-weight-bold">
                                                @if(isset($jadwal->kuota))
                                                    {{ $jadwal->kuota }}
                                                @else
                                                    {{ $jadwal->jumlah }}
                                                @endif
                                                /{{ $jadwal->jumlah }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('pendaftaran.show', $jadwal->id) }}" class="btn btn-success btn-block">
                                            <i class="fas fa-clipboard-list fa-sm mr-1"></i> Daftar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> Tidak ada jadwal praktek tersedia untuk besok.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Jadwal Mendatang -->
                <div class="tab-pane fade" id="future" role="tabpanel">
                    <div class="row mt-3">
                        @forelse($jadwalMendatang as $jadwal)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100">
                                <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between bg-info text-white">
                                    <h6 class="m-0 font-weight-bold">{{ $jadwal->dokter->poliklinik->nama_poliklinik }}</h6>
                                    <span class="badge badge-light">{{ \Carbon\Carbon::parse($jadwal->tanggal_praktek)->format('d M Y') }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto mr-3">
                                            @if(isset($jadwal->dokter->foto_dokter) && $jadwal->dokter->foto_dokter)
                                                <img src="{{ asset('storage/foto_dokter/' . $jadwal->dokter->foto_dokter) }}" class="img-fluid rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('template/img/doctor.jpg') }}" class="img-fluid rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            @endif
                                        </div>
                                        <div class="col">
                                            <h5 class="font-weight-bold">{{ $jadwal->dokter->nama_dokter }}</h5>
                                            
                                            <!-- Rating Stars -->
                                            <div class="mb-2">
                                                @php
                                                    $rating = isset($dokterRatings[$jadwal->dokter_id]) ? $dokterRatings[$jadwal->dokter_id] : 5;
                                                    $fullStars = floor($rating);
                                                    $halfStar = $rating - $fullStars >= 0.5;
                                                @endphp
                                                
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $fullStars)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @elseif($i == $fullStars + 1 && $halfStar)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            
                                            <p class="mb-1"><i class="fas fa-clock text-gray-500 mr-1"></i> {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</p>
                                            <p class="mb-1"><i class="fas fa-user-plus text-gray-500 mr-1"></i> Kuota: 
                                                <span class="font-weight-bold">
                                                @if(isset($jadwal->kuota))
                                                    {{ $jadwal->kuota }}
                                                @else
                                                    {{ $jadwal->jumlah }}
                                                @endif
                                                /{{ $jadwal->jumlah }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('pendaftaran.show', $jadwal->id) }}" class="btn btn-info btn-block">
                                            <i class="fas fa-clipboard-list fa-sm mr-1"></i> Daftar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> Tidak ada jadwal praktek tersedia untuk masa mendatang.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Registration History -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Pendaftaran Terakhir</h6>
            <a href="{{ route('pendaftaran.history') }}" class="btn btn-sm btn-primary">
                Lihat Semua
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal Daftar</th>
                            <th>Poliklinik</th>
                            <th>Dokter</th>
                            <th>Tanggal Berobat</th>
                            <th>Penjamin</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPendaftaran as $pendaftaran)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($pendaftaran->created_at)->format('d/m/Y H:i') }}</td>
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
                                    <small>{{ substr($pendaftaran->jadwalpoliklinik->jam_mulai, 0, 5) }} - {{ substr($pendaftaran->jadwalpoliklinik->jam_selesai, 0, 5) }}</small>
                                @else
                                    <span class="text-danger">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($pendaftaran->penjamin == 'UMUM')
                                    <span class="badge badge-success">UMUM</span>
                                @elseif($pendaftaran->penjamin == 'BPJS')
                                    <span class="badge badge-info">BPJS</span>
                                @else
                                    <span class="badge badge-warning">Asuransi</span>
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
                                    <i class="fas fa-file-pdf"></i> Cetak
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada riwayat pendaftaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Show the tab based on URL hash if present
        let hash = window.location.hash;
        if (hash) {
            $('#jadwalTab a[href="'+hash+'"]').tab('show');
        }

        // Change URL hash when tab is clicked
        $('#jadwalTab a').on('click', function(e) {
            window.location.hash = $(this).attr('href');
        });
    });
</script>
@endpush
@endsection
