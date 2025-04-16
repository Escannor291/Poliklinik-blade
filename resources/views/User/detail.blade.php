@extends(auth()->user()->role === 'admin' ? 'layout.admin' : (auth()->user()->role === 'petugas' ? 'layout.petugas' : 'layout.pasien'))

@section('title', 'Detail Data Diri')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Data Diri</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="img-profile rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <img src="{{ asset('template/img/undraw_profile.svg') }}" class="img-profile rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <tr>
                                        <th width="30%">Nama User</th>
                                        <td>{{ auth()->user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email/Username</th>
                                        <td>{{ auth()->user()->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>No Telepon</th>
                                        <td>{{ auth()->user()->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td>{{ ucfirst(auth()->user()->role) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="text-right mt-3">
                                <a href="{{ route('user.profile') }}" class="btn btn-primary">
                                    <i class="fas fa-edit fa-sm fa-fw mr-2"></i>Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
