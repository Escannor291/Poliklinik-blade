@extends(Auth::user()->roles == 'admin' ? 'layout.admin' : (Auth::user()->roles == 'petugas' ? 'layout.petugas' : 'layout.pasien'))

@section('title', 'Form Pendaftaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Pendaftaran</h1>
        <a href="{{ Auth::user()->roles == 'pasien' ? route('pendaftaran.pasien') : route('pendaftaran.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

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
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Jadwal</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if(isset($jadwal->dokter->foto_dokter) && $jadwal->dokter->foto_dokter)
                            <img src="{{ asset('storage/foto_dokter/' . $jadwal->dokter->foto_dokter) }}" class="img-fluid rounded-circle mb-3" style="max-width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('template/img/doctor.jpg') }}" class="img-fluid rounded-circle mb-3" style="max-width: 150px; height: 150px; object-fit: cover;">
                        @endif
                        <h5 class="font-weight-bold text-primary">Dr. {{ $jadwal->dokter->nama_dokter }}</h5>
                        <p class="text-muted">{{ $jadwal->dokter->poliklinik->nama_poliklinik }}</p>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th>Tanggal Praktik</th>
                            <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_praktek)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Jam Praktik</th>
                            <td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                        </tr>
                        <tr>
                            <th>Kuota Tersedia</th>
                            <td>{{ isset($jadwal->kuota) ? $jadwal->kuota : $jadwal->jumlah }} dari {{ $jadwal->jumlah }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Pendaftaran</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="jadwalpoliklinik_id" value="{{ $jadwal->id }}">

                        @if(Auth::user()->roles == 'admin' || Auth::user()->roles == 'petugas')
                        <div class="form-group">
                            <label for="pasien_id">Pilih Pasien <span class="text-danger">*</span></label>
                            <select name="pasien_id" id="pasien_id" class="form-control @error('pasien_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Pasien --</option>
                                @foreach(\App\Models\Datapasien::orderBy('nama_pasien')->get() as $pasien)
                                    <option value="{{ $pasien->id }}">{{ $pasien->nama_pasien }} - {{ $pasien->no_telp ?? 'Tanpa No. Telp' }}</option>
                                @endforeach
                            </select>
                            @error('pasien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                        <div class="form-group">
                            <label>Nama Pasien</label>
                            <input type="text" class="form-control" value="{{ $datapasien->nama_pasien ?? Auth::user()->nama_user }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <input type="text" class="form-control" value="{{ $datapasien->jenis_kelamin ?? 'Belum diisi' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input type="text" class="form-control" value="{{ $datapasien->no_telp ?? Auth::user()->no_telepon }}" readonly>
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="penjamin">Penjamin <span class="text-danger">*</span></label>
                            <select class="form-control @error('penjamin') is-invalid @enderror" id="penjamin" name="penjamin" required>
                                <option value="">-- Pilih Penjamin --</option>
                                <option value="UMUM" {{ old('penjamin') == 'UMUM' ? 'selected' : '' }}>UMUM</option>
                                
                                @if(Auth::user()->roles == 'admin' || Auth::user()->roles == 'petugas')
                                    <option value="BPJS" {{ old('penjamin') == 'BPJS' ? 'selected' : '' }}>BPJS</option>
                                    <option value="Asuransi" {{ old('penjamin') == 'Asuransi' ? 'selected' : '' }}>Asuransi</option>
                                @else
                                    @if($datapasien && !empty($datapasien->no_bpjs) && !empty($datapasien->scan_bpjs))
                                        <option value="BPJS" {{ old('penjamin') == 'BPJS' ? 'selected' : '' }}>BPJS</option>
                                    @elseif($datapasien)
                                        <option value="BPJS" disabled>BPJS (Data BPJS belum lengkap)</option>
                                    @endif
                                    
                                    @if($datapasien && !empty($datapasien->scan_asuransi))
                                        <option value="Asuransi" {{ old('penjamin') == 'Asuransi' ? 'selected' : '' }}>Asuransi</option>
                                    @elseif($datapasien)
                                        <option value="Asuransi" disabled>Asuransi (Data Asuransi belum lengkap)</option>
                                    @endif
                                @endif
                            </select>
                            @error('penjamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if(Auth::user()->roles == 'pasien' && $datapasien && (empty($datapasien->no_bpjs) || empty($datapasien->scan_bpjs) || empty($datapasien->scan_asuransi)))
                                <div class="small text-info mt-2">
                                    <i class="fas fa-info-circle"></i> 
                                    Untuk menggunakan BPJS atau Asuransi, lengkapi data di <a href="{{ route('pasien.edit', $datapasien->id) }}">halaman profil</a>
                                </div>
                            @endif
                        </div>

                        <div id="bpjs_section" style="display: none;">
                            <div class="form-group">
                                <label for="scan_surat_rujukan">Upload Surat Rujukan BPJS <span class="text-danger">*</span></label>
                                <input type="file" class="form-control-file @error('scan_surat_rujukan') is-invalid @enderror" id="scan_surat_rujukan" name="scan_surat_rujukan">
                                <small class="form-text text-muted">Format: JPG, PNG, PDF. Max: 2MB</small>
                                @error('scan_surat_rujukan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save fa-sm mr-1"></i> Daftar Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Show/hide insurance sections based on selected penjamin
        $('#penjamin').change(function() {
            if ($(this).val() === 'BPJS') {
                $('#bpjs_section').show();
            } else {
                $('#bpjs_section').hide();
            }
        });
        
        // Trigger change on page load
        $('#penjamin').trigger('change');
        
        // Initialize select2 if available
        if ($.fn.select2) {
            $('#pasien_id').select2({
                placeholder: "Pilih pasien...",
                width: '100%'
            });
        }
    });
</script>
@endpush
@endsection
