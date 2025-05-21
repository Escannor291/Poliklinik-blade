<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Pendaftaran Pasien</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 10px;
        }
        .hospital-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .hospital-address {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #4e73df;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .patient-barcode {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="hospital-name">RUMAH SAKIT FACHRI</div>
        <div class="hospital-address">Jl. Kesehatan No. 123, Kota Sehat</div>
        <div class="hospital-address">Telp. (021) 1234-5678 | Email: info@rsfachri.com</div>
    </div>
    
    <div class="document-title">BUKTI PENDAFTARAN PASIEN</div>
    
    <div class="section">
        <div class="section-title">Data Pasien</div>
        <table>
            <tr>
                <th width="30%">Nama Pasien</th>
                <td width="70%">{{ $pendaftaran->nama_pasien }}</td>
            </tr>
            @if($pendaftaran->datapasien)
            <tr>
                <th>NIK</th>
                <td>{{ $pendaftaran->datapasien->nik ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Lahir</th>
                <td>{{ $pendaftaran->datapasien->tempat_lahir ?? '' }} {{ $pendaftaran->datapasien->tanggal_lahir ? ', '.\Carbon\Carbon::parse($pendaftaran->datapasien->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Jenis Kelamin</th>
                <td>{{ $pendaftaran->datapasien->jenis_kelamin ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $pendaftaran->datapasien->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <th>No. Telepon</th>
                <td>{{ $pendaftaran->datapasien->no_telp ?? '-' }}</td>
            </tr>
            @endif
        </table>
    </div>
    
    <div class="section">
        <div class="section-title">Informasi Pendaftaran</div>
        <table>
            <tr>
                <th width="30%">No. Pendaftaran</th>
                <td width="70%">REG/{{ str_pad($pendaftaran->id, 6, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <th>Tanggal Pendaftaran</th>
                <td>{{ \Carbon\Carbon::parse($pendaftaran->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Jenis Penjamin</th>
                <td>{{ $pendaftaran->penjamin }}</td>
            </tr>
            @if($pendaftaran->penjamin == 'BPJS' && $pendaftaran->datapasien)
            <tr>
                <th>No. BPJS</th>
                <td>{{ $pendaftaran->datapasien->no_bpjs ?? '-' }}</td>
            </tr>
            @endif
        </table>
    </div>
    
    <div class="section">
        <div class="section-title">Informasi Kunjungan</div>
        <table>
            @if($pendaftaran->jadwalpoliklinik)
            <tr>
                <th width="30%">Poliklinik</th>
                <td width="70%">{{ $pendaftaran->jadwalpoliklinik->dokter->poliklinik->nama_poliklinik ?? '-' }}</td>
            </tr>
            <tr>
                <th>Dokter</th>
                <td>{{ $pendaftaran->jadwalpoliklinik->dokter->nama_dokter ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Praktek</th>
                <td>{{ $tanggal_praktek }}</td>
            </tr>
            <tr>
                <th>Jam Praktek</th>
                <td>{{ substr($pendaftaran->jadwalpoliklinik->jam_mulai, 0, 5) }} - {{ substr($pendaftaran->jadwalpoliklinik->jam_selesai, 0, 5) }}</td>
            </tr>
            @endif
            @if($antrian)
            <tr>
                <th>Nomor Antrian</th>
                <td><strong>{{ $antrian->no_antrian }}</strong></td>
            </tr>
            @endif
        </table>
    </div>
    
    <div class="alert-info">
        <strong>PENTING:</strong> Harap hadir 30 menit sebelum jadwal pemeriksaan. Bawa bukti pendaftaran ini dan kartu identitas.
    </div>
    
    <div class="patient-barcode">
        {!! DNS1D::getBarcodeHTML('REG-'.$pendaftaran->id, 'C39+', 1.5, 40) !!}
        <p>REG-{{ str_pad($pendaftaran->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>
    
    <div class="footer">
        <p>Bukti Pendaftaran ini sah secara elektronik tanpa tanda tangan basah</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
