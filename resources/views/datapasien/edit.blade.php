{{-- Make sure your form has enctype attribute for file uploads --}}

<form action="{{ route('pasien.update', $datapasien->id) }}" method="POST" enctype="multipart/form-data">
    @csrfors->any())
    @method('PUT')ert alert-danger">
        <ul>
    {{-- Navigation tabs for better organization --}}
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab">Data Pribadi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab">Kontak</a>
        </li>success message -->
        <li class="nav-item">
            <a class="nav-link" id="document-tab" data-toggle="tab" href="#document" role="tab">Dokumen</a>
        </li>ssion('success') }}
        <li class="nav-item"> class="close" data-dismiss="alert" aria-label="Close">
            <a class="nav-link" id="insurance-tab" data-toggle="tab" href="#insurance" role="tab">Asuransi</a>
        </li>ton>
    </ul>>
    if
    <div class="tab-content mt-3" id="myTabContent">
        {{-- Personal Data Tab --}}
        <div class="tab-pane fade show active" id="personal" role="tabpanel">
            {{-- Personal data fields here --}}sible fade show" role="alert">
            {{-- ...existing code... --}}
        </div>n type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        {{-- Contact Tab --}}
        <div class="tab-pane fade" id="contact" role="tabpanel">
            {{-- Contact fields here --}}
            {{-- ...existing code... --}}
        </div>{{ route('pasien.update', $datapasien->id) }}" method="POST" enctype="multipart/form-data">
        f
        {{-- Document Tab --}}
        <div class="tab-pane fade" id="document" role="tabpanel">
            {{-- Document upload fields here --}}--}}
            {{-- ...existing code... --}}ole="tablist">
        </div>ass="nav-item">
            <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab">Data Pribadi</a>
        {{-- Insurance Tab --}}
        <div class="tab-pane fade" id="insurance" role="tabpanel">
            <div class="card">" id="contact-tab" data-toggle="tab" href="#contact" role="tab">Kontak</a>
                <div class="card-header">Data Asuransi</div>
                <div class="card-body">
                    {{-- BPJS Section --}}nt-tab" data-toggle="tab" href="#document" role="tab">Dokumen</a>
                    <h5>Data BPJS</h5>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nomor BPJS</label>ance" role="tab">Asuransi</a>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="no_bpjs" value="{{ old('no_bpjs', $datapasien->no_bpjs) }}">
                        </div>
                    </div>t mt-3" id="myTabContent">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Scan Kartu BPJS</label>
                        <div class="col-sm-9">}
                            <input type="file" name="scan_bpjs" class="form-control-file">
                            @if($datapasien->scan_bpjs)
                                <div class="mt-2">
                                    <a href="{{ Storage::url(str_replace('public/', '', $datapasien->scan_bpjs)) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye mr-1"></i> Lihat Kartu BPJS
                                    </a>}
                                </div>-}}
                            @endif
                        </div>
                    </div>--}}
                    tab-pane fade" id="document" role="tabpanel">
                    <hr>t upload fields here --}}
                    existing code... --}}
                    {{-- Asuransi Section --}}
                    <h5>Data Asuransi Lain</h5>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Scan Kartu Asuransi</label>
                        <div class="col-sm-9">
                            <input type="file" name="scan_asuransi" class="form-control-file">
                            @if($datapasien->scan_keaslian || $datapasien->scan_asuransi)
                                <div class="mt-2">
                                    <a href="{{ Storage::url(str_replace('public/', '', $datapasien->scan_keaslian ?? $datapasien->scan_asuransi)) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye mr-1"></i> Lihat Kartu Asuransi
                                    </a>l-sm-3 col-form-label">Nomor BPJS</label>
                                </div>l-sm-9">
                            @endif type="text" class="form-control" name="no_bpjs" value="{{ old('no_bpjs', $datapasien->no_bpjs) }}">
                            <small class="form-text text-muted">Upload scan kartu asuransi Anda (format: JPG, PNG, atau PDF, max 2MB)</small>
                        </div>
                    </div>lass="form-group row">
                </div>  <label class="col-sm-3 col-form-label">Scan Kartu BPJS</label>
            </div>      <div class="col-sm-9">
        </div>              <input type="file" name="scan_bpjs" class="form-control-file">
    </div>                  @if($datapasien->scan_bpjs)
                                <div class="mt-2">
    <div class="form-group text-center mt-4">{{ Storage::url(str_replace('public/', '', $datapasien->scan_bpjs)) }}" target="_blank" class="btn btn-sm btn-info">
        <button type="submit" class="btn btn-primary px-5">e mr-1"></i> Lihat Kartu BPJS
            <i class="fas fa-save mr-1"></i> Simpan Perubahan
        </button>               </div>
    </div>                  @endif
</form>                 </div>
                    </div>















@endpush</script>    });        });            $('body').append('<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; display: flex; justify-content: center; align-items: center;"><div class="spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div></div>');            // Optional: Add a loading overlay                        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');            // Disable submit button to prevent double submission        $('form').on('submit', function() {    $(document).ready(function() {    // Show loading indicator when form is submitted<script>@push('scripts')                    
                    <hr>
                    
                    {{-- Asuransi Section --}}
                    <h5>Data Asuransi Lain</h5>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Scan Kartu Asuransi</label>
                        <div class="col-sm-9">
                            <input type="file" name="scan_asuransi" class="form-control-file">
                            @if($datapasien->scan_keaslian || $datapasien->scan_asuransi)
                                <div class="mt-2">
                                    <a href="{{ Storage::url(str_replace('public/', '', $datapasien->scan_keaslian ?? $datapasien->scan_asuransi)) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye mr-1"></i> Lihat Kartu Asuransi
                                    </a>
                                </div>
                            @endif
                            <small class="form-text text-muted">Upload scan kartu asuransi Anda (format: JPG, PNG, atau PDF, max 2MB)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-group text-center mt-4">
        <button type="submit" class="btn btn-primary px-5">
            <i class="fas fa-save mr-1"></i> Simpan Perubahan
        </button>
    </div>
</form>
