@extends('layout.admin')

@section('title', 'Edit Pendaftaran')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Pendaftaran</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('laporan_pendaftaran.update', $pendaftaran->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Pasien</label>
                            <input type="text" class="form-control" value="{{ $pendaftaran->nama_pasien }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jadwalpoliklinik_id" class="form-label">Jadwal Poliklinik</label>
                            <select class="form-select" id="jadwalpoliklinik_id" name="jadwalpoliklinik_id" required>
                                @foreach($jadwalpolikliniks as $jadwal)
                                    <option value="{{ $jadwal->id }}" {{ $pendaftaran->jadwalpoliklinik_id == $jadwal->id ? 'selected' : '' }}>
                                        {{ $jadwal->tanggal_praktek }} | {{ $jadwal->dokter->nama_dokter }} | {{ $jadwal->dokter->poliklinik->nama_poliklinik }} | {{ substr($jadwal->jam_mulai, 0, 5) }}-{{ substr($jadwal->jam_selesai, 0, 5) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="penjamin" class="form-label">Penjamin</label>
                            <select class="form-select" id="penjamin" name="penjamin" required>
                                <option value="UMUM" {{ $pendaftaran->penjamin == 'UMUM' ? 'selected' : '' }}>UMUM</option>
                                <option value="BPJS" {{ $pendaftaran->penjamin == 'BPJS' ? 'selected' : '' }}>BPJS</option>
                                <option value="Asuransi" {{ $pendaftaran->penjamin == 'Asuransi' ? 'selected' : '' }}>Asuransi</option>
                            </select>
                        </div>

                        <div id="bpjs_file_upload" class="mb-3" {{ $pendaftaran->penjamin != 'BPJS' ? 'style=display:none;' : '' }}>
                            <label for="scan_surat_rujukan" class="form-label">Scan Surat Rujukan</label>
                            <input type="file" class="form-control" id="scan_surat_rujukan" name="scan_surat_rujukan">
                            <small class="text-muted">Format: JPEG, PNG, atau PDF (Maks. 2MB)</small>
                            
                            @if($pendaftaran->scan_surat_rujukan)
                            <div class="mt-2">
                                <p>File yang sudah ada: 
                                    <a href="{{ Storage::url(str_replace('public/', '', $pendaftaran->scan_surat_rujukan)) }}" target="_blank">
                                        Lihat File
                                    </a>
                                </p>
                            </div>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('laporan_pendaftaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const penjaminSelect = document.getElementById('penjamin');
    const bpjsFileUpload = document.getElementById('bpjs_file_upload');
    
    penjaminSelect.addEventListener('change', function() {
        if (this.value === 'BPJS') {
            bpjsFileUpload.style.display = 'block';
        } else {
            bpjsFileUpload.style.display = 'none';
        }
    });
});
</script>
@endsection
