@extends('layout.admin')

@section('title', 'Pendaftaran Pasien Baru')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pendaftaran Pasien oleh Admin</h1>
        <a href="{{ route('pendaftaran.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

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

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Pendaftaran Pasien</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('pendaftaran.store-admin') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group row">
                    <label for="pasien_id" class="col-sm-3 col-form-label">Pasien <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <select class="form-control @error('pasien_id') is-invalid @enderror" id="pasien_id" name="pasien_id" required>
                                <option value="">-- Pilih Pasien --</option>
                                @foreach($patients as $pasien)
                                <option value="{{ $pasien->id }}" {{ old('pasien_id') == $pasien->id ? 'selected' : '' }}>
                                    {{ $pasien->nama_pasien }} - {{ $pasien->no_telp ?? 'No. Telp tidak tersedia' }}
                                </option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <a href="{{ route('pasien.create') }}" class="btn btn-secondary" target="_blank" title="Tambah Pasien Baru">
                                    <i class="fas fa-plus"></i> Baru
                                </a>
                            </div>
                        </div>
                        @error('pasien_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="jadwalpoliklinik_id" class="col-sm-3 col-form-label">Jadwal Dokter <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select class="form-control @error('jadwalpoliklinik_id') is-invalid @enderror" id="jadwalpoliklinik_id" name="jadwalpoliklinik_id" required>
                            <option value="">-- Pilih Jadwal Dokter --</option>
                            @foreach($jadwals->groupBy(function($item) { return \Carbon\Carbon::parse($item->tanggal_praktek)->format('Y-m-d'); }) as $date => $jadwalGroup)
                            <optgroup label="{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}">
                                @foreach($jadwalGroup as $jadwal)
                                <option value="{{ $jadwal->id }}" {{ old('jadwalpoliklinik_id') == $jadwal->id ? 'selected' : '' }}>
                                    {{ $jadwal->dokter->poliklinik->nama_poliklinik }} - 
                                    Dr. {{ $jadwal->dokter->nama_dokter }} - 
                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} - 
                                    (Kuota: {{ isset($jadwal->kuota) ? $jadwal->kuota : $jadwal->jumlah }})
                                </option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                        @error('jadwalpoliklinik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="penjamin" class="col-sm-3 col-form-label">Penjamin <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select class="form-control @error('penjamin') is-invalid @enderror" id="penjamin" name="penjamin" required>
                            <option value="">-- Pilih Penjamin --</option>
                            <option value="UMUM" {{ old('penjamin') == 'UMUM' ? 'selected' : '' }}>UMUM</option>
                            <option value="BPJS" {{ old('penjamin') == 'BPJS' ? 'selected' : '' }}>BPJS</option>
                            <option value="Asuransi" {{ old('penjamin') == 'Asuransi' ? 'selected' : '' }}>Asuransi</option>
                        </select>
                        @error('penjamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div id="bpjs_section" style="display: none;">
                    <div class="form-group row">
                        <label for="scan_surat_rujukan" class="col-sm-3 col-form-label">Surat Rujukan BPJS <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control-file @error('scan_surat_rujukan') is-invalid @enderror" id="scan_surat_rujukan" name="scan_surat_rujukan">
                            <small class="form-text text-muted">Format file: JPG, PNG, PDF. Max: 2MB</small>
                            @error('scan_surat_rujukan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Submit Pendaftaran
                        </button>
                        <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Add Select2 for better dropdown experience
        if ($.fn.select2) {
            $('#pasien_id, #jadwalpoliklinik_id').select2({
                placeholder: "-- Pilih --",
                width: '100%'
            });
        }
        
        // Show/hide BPJS section based on selected penjamin
        $('#penjamin').change(function() {
            if ($(this).val() === 'BPJS') {
                $('#bpjs_section').slideDown();
            } else {
                $('#bpjs_section').slideUp();
            }
        });
        
        // Trigger change on page load if value is pre-selected
        if ($('#penjamin').val() === 'BPJS') {
            $('#bpjs_section').show();
        }
    });
</script>
@endpush
