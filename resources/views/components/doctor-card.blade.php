<div class="card h-100 shadow-sm doctor-card">
    <div class="card-header text-white {{ $headerClass ?? 'bg-primary' }}">
        <h6 class="m-0">{{ $poliklinik }}</h6>
        <small>{{ $tanggal }}</small>
    </div>
    <div class="card-body text-center">
        <div class="mb-3">
            @if(isset($foto) && $foto)
                <img src="{{ asset('storage/foto_dokter/' . $foto) }}" class="img-fluid rounded-circle doctor-photo" alt="{{ $nama }}">
            @else
                <img src="{{ asset('template/img/doctor.jpg') }}" class="img-fluid rounded-circle doctor-photo" alt="{{ $nama }}">
            @endif
        </div>
        <h5 class="card-title">Dr. {{ $nama }}</h5>
        <div class="mb-2">
            @for($i = 0; $i < 5; $i++)
                <i class="fas fa-star text-warning"></i>
            @endfor
        </div>
        <p class="card-text">
            <i class="far fa-clock mr-1"></i> {{ $jadwal }}
        </p>
        <p class="card-text">
            <span class="badge {{ $kuota > 0 ? 'badge-success' : 'badge-danger' }}">
                Kuota: {{ $kuotaText }}
            </span>
        </p>
    </div>
    <div class="card-footer bg-white">
        {{ $slot }}
    </div>
</div>

@push('styles')
<style>
    .doctor-photo {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 3px solid #4e73df;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush
