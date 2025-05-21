@extends(Auth::user()->roles == 'admin' ? 'layout.admin' : (Auth::user()->roles == 'petugas' ? 'layout.petugas' : 'layout.pasien'))

@section('title', 'Pendaftaran Poliklinik')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-2 text-gray-800">Pendaftaran Rawat Jalan</h1>
    </div>
    
    <p class="mb-4">Daftar Jadwal Dokter</p>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error') || session('msg'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') ?? session('msg') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Alert if no jadwal is available -->
    @if($jadwalHariIni->isEmpty() && $jadwalBesok->isEmpty() && $jadwalMendatang->isEmpty())
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada jadwal praktek yang tersedia saat ini.
    </div>
    @endif

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="jadwalTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="today-tab" data-toggle="tab" href="#hari-ini" role="tab" aria-controls="hari-ini" aria-selected="true">Hari Ini</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tomorrow-tab" data-toggle="tab" href="#tomorrow" role="tab" aria-controls="tomorrow" aria-selected="false">Besok</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="future-tab" data-toggle="tab" href="#future" role="tab" aria-controls="future" aria-selected="false">Mendatang</a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content" id="jadwalTabContent">
        <!-- Jadwal Hari Ini -->
        <div class="tab-pane fade show active" id="hari-ini" role="tabpanel" aria-labelledby="hari-ini-tab">
            <div class="row">
                @forelse($jadwalHariIni as $jadwal)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card jadwal-card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">{{ $jadwal->dokter->poliklinik->nama_poliklinik }}</h6>
                            <small>{{ \Carbon\Carbon::parse($jadwal->tanggal_praktek)->format('d M Y') }}</small>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if(isset($jadwal->dokter->foto_dokter) && $jadwal->dokter->foto_dokter)
                                    <img src="{{ asset('storage/foto_dokter/' . $jadwal->dokter->foto_dokter) }}" class="img-fluid rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('template/img/doctor.jpg') }}" class="img-fluid rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                @endif
                            </div>
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
                                @if(isset($jadwal->kuota))
                                    <span class="font-weight-bold">{{ $jadwal->kuota }}/{{ $jadwal->jumlah }}</span>
                                @else
                                    <span class="font-weight-bold">{{ $jadwal->jumlah }}/{{ $jadwal->jumlah }}</span>
                                @endif
                            </p>
                            <div class="text-center mt-3">
                                <a href="{{ route('pendaftaran.show', $jadwal->id) }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-clipboard-list fa-sm"></i> Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Tidak ada jadwal praktek tersedia untuk hari ini.
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Tambahkan debugging info jika dalam mode debug -->
        @if(config('app.debug'))
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-danger">Debug Info (Hanya tampil di development)</h6>
            </div>
            <div class="card-body">
                <p>Waktu sekarang: {{ Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') }}</p>
                <p>Jumlah jadwal hari ini: {{ $jadwalHariIni->count() }}</p>
                @foreach($jadwalHariIni as $jadwal)
                    <div class="mb-2">
                        <strong>ID: {{ $jadwal->id }}</strong> - 
                        Dokter: {{ $jadwal->dokter->nama_dokter }} - 
                        Jam: {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }} - 
                        Kuota: {{ $jadwal->kuota ?? $jadwal->jumlah }}/{{ $jadwal->jumlah }}
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Jadwal Besok -->
        <div class="tab-pane fade" id="tomorrow" role="tabpanel" aria-labelledby="tomorrow-tab">
            <div class="row mt-4">
                @forelse($jadwalBesok as $jadwal)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card jadwal-card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">{{ $jadwal->dokter->poliklinik->nama_poliklinik }}</h6>
                            <small>{{ \Carbon\Carbon::parse($jadwal->tanggal_praktek)->format('d M Y') }}</small>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if(isset($jadwal->dokter->foto_dokter) && $jadwal->dokter->foto_dokter)
                                    <img src="{{ asset('storage/foto_dokter/' . $jadwal->dokter->foto_dokter) }}" class="img-fluid rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('template/img/doctor.jpg') }}" class="img-fluid rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                @endif
                            </div>
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
                                @if(isset($jadwal->kuota))
                                    <span class="font-weight-bold">{{ $jadwal->kuota }}/{{ $jadwal->jumlah }}</span>
                                @else
                                    <span class="font-weight-bold">{{ $jadwal->jumlah }}/{{ $jadwal->jumlah }}</span>
                                @endif
                            </p>
                            <div class="text-center mt-3">
                                <a href="{{ route('pendaftaran.show', $jadwal->id) }}" class="btn btn-info btn-block">
                                    <i class="fas fa-notes-medical mr-1"></i> Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Tidak ada jadwal praktek tersedia untuk besok</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Jadwal Mendatang -->
        <div class="tab-pane fade" id="future" role="tabpanel" aria-labelledby="future-tab">
            <div class="row mt-4">
                @forelse($jadwalMendatang as $jadwal)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card jadwal-card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">{{ $jadwal->dokter->poliklinik->nama_poliklinik }}</h6>
                            <small>{{ \Carbon\Carbon::parse($jadwal->tanggal_praktek)->format('d M Y') }}</small>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if(isset($jadwal->dokter->foto_dokter) && $jadwal->dokter->foto_dokter)
                                    <img src="{{ asset('storage/foto_dokter/' . $jadwal->dokter->foto_dokter) }}" class="img-fluid rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('template/img/doctor.jpg') }}" class="img-fluid rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                @endif
                            </div>
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
                                @if(isset($jadwal->kuota))
                                    <span class="font-weight-bold">{{ $jadwal->kuota }}/{{ $jadwal->jumlah }}</span>
                                @else
                                    <span class="font-weight-bold">{{ $jadwal->jumlah }}/{{ $jadwal->jumlah }}</span>
                                @endif
                            </p>
                            <div class="text-center mt-3">
                                <a href="{{ route('pendaftaran.show', $jadwal->id) }}" class="btn btn-success btn-block">
                                    <i class="fas fa-notes-medical mr-1"></i> Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Tidak ada jadwal praktek tersedia untuk masa mendatang</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

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