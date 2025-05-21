@extends(Auth::user()->roles == 'admin' ? 'layout.admin' : (Auth::user()->roles == 'petugas' ? 'layout.petugas' : 'layout.pasien'))

@section('title', 'Error')

@section('content')
<div class="container-fluid">
    <div class="text-center">
        <div class="error mx-auto" data-text="Error">Error</div>
        <p class="lead text-gray-800 mb-5">{{ $message }}</p>
        <p class="text-gray-500 mb-0">Sepertinya terjadi masalah dengan sistem...</p>
        <a href="{{ route('pendaftaran.index') }}">&larr; Kembali ke Halaman Pendaftaran</a>
    </div>
</div>
@endsection
