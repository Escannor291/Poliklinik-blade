@extends('layout.admin')

@section('title', 'Laporan Pendaftaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Laporan Pendaftaran</h1>
    <p class="mb-4">Laporan data pendaftaran pasien.</p>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan_pendaftaran.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="poliklinik">Poliklinik</label>
                        <select class="form-control" id="poliklinik" name="poliklinik">
                            <option value="">Semua Poliklinik</option>
                            @foreach($polikliniks as $poliklinik)
                                <option value="{{ $poliklinik->id }}" {{ request('poliklinik') == $poliklinik->id ? 'selected' : '' }}>
                                    {{ $poliklinik->nama_poliklinik }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="dokter">Dokter</label>
                        <select class="form-control" id="dokter" name="dokter">
                            <option value="">Semua Dokter</option>
                            @foreach($dokters as $dokter)
                                <option value="{{ $dokter->id }}" {{ request('dokter') == $dokter->id ? 'selected' : '' }}>
                                    {{ $dokter->nama_dokter }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter fa-sm"></i> Filter
                        </button>
                        <a href="{{ route('laporan_pendaftaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt fa-sm"></i> Reset
                        </a>
                    </div>
                    <!-- Removed the Export PDF button here -->
                </div>
            </form>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Pendaftaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Berobat</th>
                            <th>No. Antrian</th>
                            <th>Nama Pasien</th>
                            <th>Poliklinik</th>
                            <th>Dokter</th>
                            <th>Penjamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftarans as $index => $pendaftaran)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($pendaftaran->jadwalpoliklinik)
                                    {{ \Carbon\Carbon::parse($pendaftaran->jadwalpoliklinik->tanggal_praktek)->format('d/m/Y') }}
                                @else
                                    <span class="text-danger">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($pendaftaran->antrian)
                                    {{ $pendaftaran->antrian->no_antrian }}
                                @else
                                    -
                                @endif
                            </td>
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
                                @if($pendaftaran->penjamin == 'UMUM')
                                    <span class="badge badge-success">UMUM</span>
                                @elseif($pendaftaran->penjamin == 'BPJS')
                                    <span class="badge badge-primary">BPJS</span>
                                @else
                                    <span class="badge badge-warning">Asuransi</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('laporan_pendaftaran.download_pdf', $pendaftaran->id) }}" class="btn btn-sm btn-danger" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                
                                @if(Auth::user()->roles == 'admin')
                                    <a href="{{ route('laporan_pendaftaran.edit', $pendaftaran->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('laporan_pendaftaran.destroy', $pendaftaran->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
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
        // Initialize DataTable
        $('#dataTable').DataTable({
            "language": {
                "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Berikutnya"
                },
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri"
            }
        });

        // Poliklinik change event handler
        $('#poliklinik').change(function() {
            var poliklinikId = $(this).val();
            if (poliklinikId) {
                $.ajax({
                    url: '{{ route('laporan_pendaftaran.get_dokters') }}',
                    type: 'GET',
                    data: { poliklinik_id: poliklinikId },
                    success: function(data) {
                        $('#dokter').empty();
                        $('#dokter').append('<option value="">Semua Dokter</option>');
                        $.each(data, function(key, value) {
                            $('#dokter').append('<option value="' + value.id + '">' + value.nama_dokter + '</option>');
                        });
                    }
                });
            } else {
                $('#dokter').empty();
                $('#dokter').append('<option value="">Semua Dokter</option>');
                @foreach($dokters as $dokter)
                    $('#dokter').append('<option value="{{ $dokter->id }}">{{ $dokter->nama_dokter }}</option>');
                @endforeach
            }
        });
    });
</script>
@endpush
