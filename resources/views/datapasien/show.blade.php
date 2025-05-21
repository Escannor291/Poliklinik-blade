@extends('layout.admin')

@section('title', 'Detail Data Pasien')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Data Pasien</h1>
        <a href="{{ route('pasien.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Personal Information Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pribadi</h6>
                    <a href="{{ route('pasien.edit', $dataPasien->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit fa-sm"></i> Edit Data
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">Nama Lengkap</th>
                                <td>{{ $dataPasien->nama_pasien }}</td>
                            </tr>
                            <tr>
                                <th>NIK</th>
                                <td>{{ $dataPasien->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tempat, Tanggal Lahir</th>
                                <td>
                                    @if($dataPasien->tempat_lahir || $dataPasien->tanggal_lahir)
                                        {{ $dataPasien->tempat_lahir ?? '' }}
                                        @if($dataPasien->tanggal_lahir)
                                            , {{ \Carbon\Carbon::parse($dataPasien->tanggal_lahir)->format('d F Y') }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>{{ $dataPasien->jenis_kelamin ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $dataPasien->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nomor Telepon</th>
                                <td>{{ $dataPasien->no_telp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $dataPasien->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>No. BPJS</th>
                                <td>{{ $dataPasien->no_bpjs ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dokumen Medis</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Kartu Identitas</h5>
                    @if($dataPasien->scan_ktp)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $dataPasien->scan_ktp) }}" alt="KTP" class="img-fluid mb-2">
                            <a href="{{ asset('storage/' . $dataPasien->scan_ktp) }}" target="_blank" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-eye fa-sm"></i> Lihat KTP
                            </a>
                        </div>
                    @else
                        <p class="text-muted">Scan KTP belum diunggah</p>
                    @endif

                    <h5 class="font-weight-bold mt-4">Kartu Berobat</h5>
                    @if($dataPasien->scan_kberobat)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $dataPasien->scan_kberobat) }}" alt="Kartu Berobat" class="img-fluid mb-2">
                            <a href="{{ asset('storage/' . $dataPasien->scan_kberobat) }}" target="_blank" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-eye fa-sm"></i> Lihat Kartu Berobat
                            </a>
                        </div>
                    @else
                        <p class="text-muted">Kartu berobat belum diunggah</p>
                    @endif

                    <h5 class="font-weight-bold mt-4">BPJS</h5>
                    @if($dataPasien->scan_bpjs)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $dataPasien->scan_bpjs) }}" alt="BPJS" class="img-fluid mb-2">
                            <a href="{{ asset('storage/' . $dataPasien->scan_bpjs) }}" target="_blank" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-eye fa-sm"></i> Lihat BPJS
                            </a>
                        </div>
                    @else
                        <p class="text-muted">Kartu BPJS belum diunggah</p>
                    @endif

                    <h5 class="font-weight-bold mt-4">Asuransi</h5>
                    @if($dataPasien->scan_asuransi)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $dataPasien->scan_asuransi) }}" alt="Asuransi" class="img-fluid mb-2">
                            <a href="{{ asset('storage/' . $dataPasien->scan_asuransi) }}" target="_blank" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-eye fa-sm"></i> Lihat Asuransi
                            </a>
                        </div>
                    @else
                        <p class="text-muted">Kartu asuransi belum diunggah</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Registration History Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Pendaftaran</h6>
        </div>
        <div class="card-body">
            @php
                $pendaftarans = \App\Models\Pendaftaran::with('jadwalpoliklinik.dokter.poliklinik')
                    ->where('id_pasien', $dataPasien->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            @endphp

            @if($pendaftarans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal Daftar</th>
                                <th>Tanggal Berobat</th>
                                <th>Dokter</th>
                                <th>Poliklinik</th>
                                <th>Penjamin</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftarans as $pendaftaran)
                                <tr>
                                    <td>{{ $pendaftaran->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($pendaftaran->jadwalpoliklinik)
                                            {{ \Carbon\Carbon::parse($pendaftaran->jadwalpoliklinik->tanggal_praktek)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($pendaftaran->jadwalpoliklinik && $pendaftaran->jadwalpoliklinik->dokter)
                                            {{ $pendaftaran->jadwalpoliklinik->dokter->nama_dokter }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($pendaftaran->jadwalpoliklinik && $pendaftaran->jadwalpoliklinik->dokter && $pendaftaran->jadwalpoliklinik->dokter->poliklinik)
                                            {{ $pendaftaran->jadwalpoliklinik->dokter->poliklinik->nama_poliklinik }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $pendaftaran->penjamin == 'BPJS' ? 'primary' : ($pendaftaran->penjamin == 'Asuransi' ? 'warning' : 'success') }}">
                                            {{ $pendaftaran->penjamin }}
                                        </span>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($pendaftarans->count() >= 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('laporan_pendaftaran.index', ['pasien_id' => $dataPasien->id]) }}" class="btn btn-primary btn-sm">
                            Lihat Semua Riwayat
                        </a>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    Pasien belum pernah melakukan pendaftaran.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
