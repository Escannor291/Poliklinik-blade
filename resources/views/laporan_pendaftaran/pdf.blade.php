<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendaftaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1, h2 {
            text-align: center;
        }
        .header {
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pendaftaran Pasien</h1>
        <h3>Tanggal: {{ date('d-m-Y') }}</h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Berobat</th>
                <th>Nama Pasien</th>
                <th>Poliklinik</th>
                <th>Dokter</th>
                <th>Penjamin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendaftarans as $key => $pendaftaran)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>
                    @if($pendaftaran->jadwalpoliklinik)
                        {{ \Carbon\Carbon::parse($pendaftaran->jadwalpoliklinik->tanggal_praktek)->format('d/m/Y') }}
                    @else
                        Jadwal tidak tersedia
                    @endif
                </td>
                <td>{{ $pendaftaran->nama_pasien }}</td>
                <td>
                    @if($pendaftaran->jadwalpoliklinik && $pendaftaran->jadwalpoliklinik->dokter && $pendaftaran->jadwalpoliklinik->dokter->poliklinik)
                        {{ $pendaftaran->jadwalpoliklinik->dokter->poliklinik->nama_poliklinik }}
                    @else
                        Data tidak tersedia
                    @endif
                </td>
                <td>
                    @if($pendaftaran->jadwalpoliklinik && $pendaftaran->jadwalpoliklinik->dokter)
                        {{ $pendaftaran->jadwalpoliklinik->dokter->nama_dokter }}
                    @else
                        Data tidak tersedia
                    @endif
                </td>
                <td>{{ $pendaftaran->penjamin }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>
