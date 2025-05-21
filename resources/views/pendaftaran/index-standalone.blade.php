<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Pendaftaran Poliklinik">
    <meta name="author" content="Fachri Hospital">
    <title>Pendaftaran Poliklinik</title>
    
    <!-- Font Awesome -->
    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fc;
        }
        .navbar-custom {
            background-color: #4e73df;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            color: white !important;
            font-weight: 700;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .nav-link:hover {
            color: white !important;
        }
        .card-jadwal {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .card-jadwal:hover {
            transform: translateY(-5px);
        }
        .footer {
            background-color: #fff;
            padding: 1rem 0;
            margin-top: 3rem;
            box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hospital-alt mr-2"></i> Fachri Hospital
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard-'.Auth::user()->roles) }}">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pendaftaran.history') }}">
                            <i class="fas fa-history mr-1"></i> Riwayat Pendaftaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.profile') }}">
                            <i class="fas fa-user-circle mr-1"></i> {{ Auth::user()->nama_user }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Pendaftaran Poliklinik</h1>
                    <a href="{{ route('dashboard-'.Auth::user()->roles) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Dashboard
                    </a>
                </div>
                
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                
                @if(session('error') || session('msg'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') ?? session('msg') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Loading jadwal... -->
        <div id="loadingSection" class="text-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat jadwal dokter...</p>
        </div>
        
        <!-- Jadwal Dokter Section -->
        <div id="jadwalSection" style="display:none;">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Jadwal Dokter</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="jadwalTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                                <i class="fas fa-calendar-alt mr-1"></i> Semua Jadwal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="today-tab" data-toggle="tab" href="#today" role="tab">
                                <i class="fas fa-calendar-day mr-1"></i> Hari Ini
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tomorrow-tab" data-toggle="tab" href="#tomorrow" role="tab">
                                <i class="fas fa-calendar-plus mr-1"></i> Besok
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="future-tab" data-toggle="tab" href="#future" role="tab">
                                <i class="fas fa-calendar-week mr-1"></i> Mendatang
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-4" id="jadwalTabContent">
                        <!-- All Jadwal Tab -->
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <div id="allJadwalContent">
                                <!-- Content will be loaded via AJAX -->
                            </div>
                        </div>
                        
                        <!-- Today Jadwal Tab -->
                        <div class="tab-pane fade" id="today" role="tabpanel">
                            <div id="todayJadwalContent">
                                <!-- Content will be loaded via AJAX -->
                            </div>
                        </div>
                        
                        <!-- Tomorrow Jadwal Tab -->
                        <div class="tab-pane fade" id="tomorrow" role="tabpanel">
                            <div id="tomorrowJadwalContent">
                                <!-- Content will be loaded via AJAX -->
                            </div>
                        </div>
                        
                        <!-- Future Jadwal Tab -->
                        <div class="tab-pane fade" id="future" role="tabpanel">
                            <div id="futureJadwalContent">
                                <!-- Content will be loaded via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-left">
                    <span>&copy; {{ date('Y') }} Fachri Hospital</span>
                </div>
                <div class="col-md-6 text-center text-md-right">
                    <a href="#" class="text-muted mr-3">Syarat & Ketentuan</a>
                    <a href="#" class="text-muted">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Simulate loading jadwal
            setTimeout(function() {
                $("#loadingSection").hide();
                $("#jadwalSection").fadeIn();
                
                // Load all jadwal data via AJAX
                $.ajax({
                    url: '{{ route("pendaftaran.get-jadwal") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Populate jadwal sections
                        if (data.all.length > 0) {
                            renderJadwalCards(data.all, "#allJadwalContent");
                        } else {
                            $("#allJadwalContent").html('<div class="text-center my-5"><i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><p>Tidak ada jadwal tersedia</p></div>');
                        }
                        
                        if (data.today.length > 0) {
                            renderJadwalCards(data.today, "#todayJadwalContent");
                        } else {
                            $("#todayJadwalContent").html('<div class="text-center my-5"><i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><p>Tidak ada jadwal tersedia untuk hari ini</p></div>');
                        }
                        
                        if (data.tomorrow.length > 0) {
                            renderJadwalCards(data.tomorrow, "#tomorrowJadwalContent");
                        } else {
                            $("#tomorrowJadwalContent").html('<div class="text-center my-5"><i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><p>Tidak ada jadwal tersedia untuk besok</p></div>');
                        }
                        
                        if (data.future.length > 0) {
                            renderJadwalCards(data.future, "#futureJadwalContent");
                        } else {
                            $("#futureJadwalContent").html('<div class="text-center my-5"><i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><p>Tidak ada jadwal tersedia untuk masa mendatang</p></div>');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat memuat jadwal dokter. Silakan refresh halaman.');
                    }
                });
            }, 1500); // Simulate loading for 1.5 seconds
            
            // Function to render jadwal cards
            function renderJadwalCards(jadwals, targetElement) {
                let html = '<div class="row">';
                
                jadwals.forEach(function(jadwal) {
                    html += `
                    <div class="col-md-4">
                        <div class="card card-jadwal shadow">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">${jadwal.dokter.nama_dokter}</h6>
                                <span class="badge badge-${getDateBadgeColor(jadwal.tanggal_praktek)}">${formatDate(jadwal.tanggal_praktek)}</span>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <img src="${jadwal.dokter.foto || '{{ asset("template/img/doctor.jpg") }}'}" alt="Dokter" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="h6 mb-0 text-primary">${jadwal.dokter.poliklinik.nama_poliklinik}</div>
                                        <div class="small text-muted">
                                            ${renderStars(5)}
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-1"><i class="fas fa-clock text-gray-400 mr-2"></i> ${formatTime(jadwal.jam_mulai)} - ${formatTime(jadwal.jam_selesai)}</p>
                                <p class="mb-3"><i class="fas fa-user-plus text-gray-400 mr-2"></i> Kuota: <strong>${jadwal.kuota || jadwal.jumlah} / ${jadwal.jumlah}</strong></p>
                                <a href="{{ url('/pendaftaran') }}/${jadwal.id}" class="btn btn-primary btn-block">
                                    <i class="fas fa-clipboard-list mr-1"></i> Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                    `;
                });
                
                html += '</div>';
                $(targetElement).html(html);
            }
            
            // Helper functions
            function formatDate(dateString) {
                const date = new Date(dateString);
                const options = { day: 'numeric', month: 'short', year: 'numeric' };
                return date.toLocaleDateString('id-ID', options);
            }
            
            function formatTime(timeString) {
                return timeString.substring(0, 5);
            }
            
            function getDateBadgeColor(dateString) {
                const today = new Date();
                today.setHours(0,0,0,0);
                
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);
                
                const date = new Date(dateString);
                date.setHours(0,0,0,0);
                
                if (date.getTime() === today.getTime()) return 'danger';
                if (date.getTime() === tomorrow.getTime()) return 'warning';
                return 'info';
            }
            
            function renderStars(rating) {
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= Math.floor(rating)) {
                        stars += '<i class="fas fa-star text-warning"></i>';
                    } else if (i - rating < 1 && i - rating > 0) {
                        stars += '<i class="fas fa-star-half-alt text-warning"></i>';
                    } else {
                        stars += '<i class="far fa-star text-warning"></i>';
                    }
                }
                return stars;
            }
        });
    </script>
</body>
</html>
