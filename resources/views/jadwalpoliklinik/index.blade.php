<!-- index.blade.php -->
@extends('layout.admin')

@section('title', 'Jadwal Poliklinik')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Data Jadwal Poliklinik</h1>

    <!-- Error Handling -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Date Range Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Jadwal Praktek</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('jadwalpoliklinik.index') }}" method="GET" class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_date">Dari Tanggal</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="end_date">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('jadwalpoliklinik.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Jadwal Poliklinik Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Praktek</h6>
            <a href="{{ route('jadwalpoliklinik.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="jadwalPoliklinikTable">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Dokter</th>
                            <th>Poliklinik</th>
                            <th>Foto Dokter</th>
                            <th>Tanggal Praktek</th>
                            <th>Jam Praktek</th>
                            <th>Kapasitas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwalpoliklinik as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->kode }}</td>
                            <td>{{ $item->dokter->nama_dokter }}</td>
                            <td>{{ $item->dokter->poliklinik->nama_poliklinik }}</td>
                            <td class="text-center">
                                @if($item->dokter->foto_dokter)
                                    <img src="{{ asset('storage/foto_dokter/' . $item->dokter->foto_dokter) }}" 
                                         alt="Foto Dokter" 
                                         class="img-thumbnail" 
                                         style="max-width: 60px; max-height: 60px;">
                                @else
                                    <span class="badge badge-secondary">Tidak Ada Foto</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_praktek)->format('d M Y') }}</td>
                            <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('jadwalpoliklinik.edit', $item->id) }}" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('jadwalpoliklinik.destroy', $item->id) }}" 
                                          method="POST" 
                                          class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-danger btn-sm btn-delete" 
                                                onclick="return confirm('Yakin hapus jadwal ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="alert alert-info">Tidak ada jadwal poliklinik</div>
                            </td>
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
    $('#jadwalPoliklinikTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });
});
</script>

@endpush

<!-- Sertakan SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ asset('js/sweetalert.js') }}"></script>
@endsection