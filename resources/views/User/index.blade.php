@extends('layout.admin')

@section('title', 'Data User')

@section('content')
<!-- Hidden inputs for SweetAlert messages -->
<input type="hidden" id="success-message" value="{{ session('success') }}">
<input type="hidden" id="error-message" value="{{ session('error') }}">

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Data User</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama User</th>
                        <th>Username</th>
                        <th>No Telepon</th>
                        <th>Foto</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($user as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->nama_user }}</td>
                        <td>{{ $item->username }}</td>
                        <td>{{ $item->no_telepon }}</td>
                        <td>
                            @if($item->foto_user)
                            <img src="{{ asset('storage/foto_user/' . $item->foto_user) }}" alt="Foto User" width="50" height="50">
                            @else
                            <span>No Image</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($item->roles) }}</td>
                        <td>
                            <a href="{{ route('user.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('user.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sweetalert.js') }}"></script>
<script>
    $(document).ready(function() {
        // SweetAlert for success and error messages
        var successMessage = $('#success-message').val();
        var errorMessage = $('#error-message').val();
        
        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMessage,
            });
        }
        
        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage,
            });
        }
        
        // SweetAlert for delete confirmation
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

@include('sweetalert::alert')