@extends('layout.pasien')

@section('title', 'Riwayat Pendaftaran')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Riwayat Pendaftaran</h1>
    <p class="mb-4">Berikut adalah riwayat pendaftaran Anda di Fachri Hospital.</p>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error') || isset($error))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') ?? $error }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pendaftaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pendaftaranTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal Daftar</th>
                            <th>Poliklinik</th>
                            <th>Dokter</th>
                            <th>Tanggal Berobat</th>
                            <th>Jam Praktek</th>
                            <th>Penjamin</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftarans as $index => $pendaftaran)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($pendaftaran->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($pendaftaran->jadwalpoliklinik && $pendaftaran->jadwalpoliklinik->dokter && $pendaftaran->jadwalpoliklinik->dokter->poliklinik)
                                    {{ $pendaftaran->jadwalpoliklinik->dokter->poliklinik->nama_poliklinik }}
                                @else
                                    <span class="text-danger">Tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($pendaftaran->jadwalpoliklinik && $pendaftaran->jadwalpoliklinik->dokter)
                                    {{ $pendaftaran->jadwalpoliklinik->dokter->nama_dokter }}
                                @else
                                    <span class="text-danger">Tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($pendaftaran->jadwalpoliklinik)
                                    {{ \Carbon\Carbon::parse($pendaftaran->jadwalpoliklinik->tanggal_praktek)->format('d/m/Y') }}
                                @else
                                    <span class="text-danger">Tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($pendaftaran->jadwalpoliklinik)
                                    {{ substr($pendaftaran->jadwalpoliklinik->jam_mulai, 0, 5) }} - {{ substr($pendaftaran->jadwalpoliklinik->jam_selesai, 0, 5) }}
                                @else
                                    <span class="text-danger">Tidak tersedia</span>
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
                            <td colspan="9" class="text-center">Tidak ada riwayat pendaftaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized
        if ($.fn.dataTable.isDataTable('#pendaftaranTable')) {
            // If already initialized, destroy it first
            $('#pendaftaranTable').DataTable().destroy();
        }
        
        // Then initialize
        $('#pendaftaranTable').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data yang tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "order": [[1, 'desc']]
        });
    });
</script>
@endpush
