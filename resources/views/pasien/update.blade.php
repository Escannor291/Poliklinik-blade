@extends('layout.pasien')

@section('title', 'Edit Data Pasien')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Data Pasien</h1>
        <a href="{{ route('pasien.show', $dataPasien->id) }}" class="btn btn-sm btn-secondary shadow-sm">
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
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

    <div class="row">
        <!-- Personal Data -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pribadi</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('pasien.update', $dataPasien->id) }}" method="POST" enctype="multipart/form-data" id="updateForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group row">
                            <label for="nama_pasien" class="col-sm-3 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="{{ old('nama_pasien', $dataPasien->nama_pasien) }}" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="no_telp" class="col-sm-3 col-form-label">Nomor Telepon</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ old('no_telp', $dataPasien->no_telp) }}">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="nik" class="col-sm-3 col-form-label">NIK</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik', $dataPasien->nik) }}">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="tempat_lahir" class="col-sm-3 col-form-label">Tempat Lahir</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $dataPasien->tempat_lahir) }}">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-sm-3 col-form-label">Tanggal Lahir</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $dataPasien->tanggal_lahir) }}">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="jenis_kelamin" class="col-sm-3 col-form-label">Jenis Kelamin</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin', $dataPasien->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin', $dataPasien->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $dataPasien->alamat) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="no_kberobat" class="col-sm-3 col-form-label">No. Kartu Berobat</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="no_kberobat" name="no_kberobat" value="{{ old('no_kberobat', $dataPasien->no_kberobat) }}">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="scan_ktp" class="col-sm-3 col-form-label">Scan KTP</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control-file" id="scan_ktp" name="scan_ktp">
                                @if($dataPasien->scan_ktp)
                                <div class="mt-2">
                                    <small class="form-text text-muted">File yang sudah diunggah: <a href="{{ asset('storage/'.$dataPasien->scan_ktp) }}" target="_blank">Lihat KTP</a></small>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="scan_kberobat" class="col-sm-3 col-form-label">Scan Kartu Berobat</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control-file" id="scan_kberobat" name="scan_kberobat">
                                @if($dataPasien->scan_kberobat)
                                <div class="mt-2">
                                    <small class="form-text text-muted">File yang sudah diunggah: <a href="{{ asset('storage/'.$dataPasien->scan_kberobat) }}" target="_blank">Lihat Kartu Berobat</a></small>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Insurance Data -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data BPJS</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('pasien.update-insurance', $dataPasien->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="insurance_type" value="bpjs">
                        
                        <div class="form-group">
                            <label for="no_bpjs">Nomor BPJS</label>
                            <input type="text" class="form-control" id="no_bpjs" name="no_bpjs" 
                                   value="{{ old('no_bpjs', $dataPasien->no_bpjs ?? '') }}">
                            @error('no_bpjs')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="scan_bpjs">Scan Kartu BPJS</label>
                            <input type="file" class="form-control-file" id="scan_bpjs" name="scan_bpjs">
                            <small class="form-text text-muted">Format: JPG, PNG, PDF. Max: 2MB</small>
                            @error('scan_bpjs')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            
                            @if(isset($dataPasien->scan_bpjs) && $dataPasien->scan_bpjs)
                            <div class="mt-2">
                                <small class="form-text text-muted">File yang sudah diunggah:</small>
                                <a href="{{ asset('storage/'.$dataPasien->scan_bpjs) }}" target="_blank" class="btn btn-sm btn-info mt-1">
                                    <i class="fas fa-eye fa-sm"></i> Lihat Kartu BPJS
                                </a>
                            </div>
                            @endif
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-1"></i> Simpan Data BPJS
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Asuransi</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('pasien.update-insurance', $dataPasien->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="insurance_type" value="asuransi">
                        
                        <div class="form-group">
                            <label for="scan_asuransi">Scan Kartu Asuransi</label>
                            <input type="file" class="form-control-file" id="scan_asuransi" name="scan_asuransi">
                            <small class="form-text text-muted">Format: JPG, PNG, PDF. Max: 2MB</small>
                            @error('scan_asuransi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            
                            @if(isset($dataPasien->scan_asuransi) && $dataPasien->scan_asuransi)
                            <div class="mt-2">
                                <small class="form-text text-muted">File yang sudah diunggah:</small>
                                <a href="{{ asset('storage/'.$dataPasien->scan_asuransi) }}" target="_blank" class="btn btn-sm btn-info mt-1">
                                    <i class="fas fa-eye fa-sm"></i> Lihat Kartu Asuransi
                                </a>
                            </div>
                            @endif
                        </div>
                        
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-save mr-1"></i> Simpan Data Asuransi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#updateForm').on('submit', function() {
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');
        });
        
        $('#bpjsForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Memproses...');
        });
        
        $('#asuransiForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Memproses...');
        });
    });
</script>
@endpush
@endsection