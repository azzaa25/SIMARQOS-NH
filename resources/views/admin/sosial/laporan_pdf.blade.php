<!DOCTYPE html>
<html>
<head>
    <title>Laporan {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        /* Pengaturan Dasar A4 */
        @page { 
            margin: 1.5cm; 
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a202c;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }
        
        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #064e3b;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #064e3b;
            margin: 0;
            text-transform: uppercase;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0 0;
            color: #4a5568;
            font-size: 11px;
        }

        /* Stats Cards - Menggunakan Tabel agar Layout Stabil */
        .stats-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 8px;
            text-align: left;
        }
        .card-masuk { border-left: 4px solid #10b981; }
        .card-keluar { border-left: 4px solid #ef4444; }
        .card-saldo { border-left: 4px solid #3b82f6; }
        
        .card-label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            font-weight: bold;
        }
        .card-value {
            font-size: 15px;
            font-weight: bold;
            margin-top: 5px;
        }

        /* Section Title */
        .section-title {
            color: #064e3b;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 3px solid #064e3b;
            padding-left: 8px;
        }

        /* Table Rincian */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.main-table th {
            background-color: #f8fafc;
            color: #4a5568;
            text-align: left;
            padding: 8px 10px;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #e2e8f0;
        }
        table.main-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-masuk { background: #d1fae5; color: #065f46; }
        .badge-keluar { background: #fee2e2; color: #991b1b; }

        /* Signature Container */
        .signature-container {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }
        .sig-box {
            float: right;
            width: 220px;
            text-align: center;
        }
        .sig-space { height: 70px; }

        /* Footer */
        .footer-note {
            margin-top: 50px;
            font-size: 9px;
            color: #718096;
            border-top: 1px solid #edf2f7;
            padding-top: 5px;
            text-align: center;
            clear: both;
        }

        /* Utility Classes */
        .text-green { color: #065f46; }
        .text-red { color: #991b1b; }
        .text-blue { color: #1e40af; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Masjid Nurul Huda</h1>
        <p>Laporan Pertanggungjawaban Dana Kegiatan Sosial</p>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    <div class="section-title">Detail Kegiatan</div>
    <table style="border: none; width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="border: none; width: 120px;">Nama Kegiatan</td>
            <td style="border: none;">: <strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
        </tr>
        <tr>
            <td style="border: none;">Kategori</td>
            <td style="border: none;">: {{ $kegiatan->kategori->nama_kategori ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td style="border: none;">Tanggal</td>
            <td style="border: none;">: {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="border: none;">Lokasi</td>
            <td style="border: none;">: {{ $kegiatan->lokasi }}</td>
        </tr>
    </table>

    <table class="stats-table">
        <tr>
            <td class="card card-masuk">
                <div class="card-label">Dana Masuk</div>
                <div class="card-value text-green">Rp {{ number_format($totalDanaMasuk, 0, ',', '.') }}</div>
            </td>
            <td class="card card-keluar">
                <div class="card-label">Dana Keluar</div>
                <div class="card-value text-red">Rp {{ number_format($totalDanaKeluar, 0, ',', '.') }}</div>
            </td>
            <td class="card card-saldo">
                <div class="card-label">Sisa Saldo Kegiatan</div>
                <div class="card-value text-blue">Rp {{ number_format($totalDanaMasuk - $totalDanaKeluar, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Rincian Transaksi (Masuk & Keluar)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th width="15%">Waktu</th>
                <th width="10%">Tipe</th>
                <th>Keterangan / Donatur</th>
                <th width="15%">Metode</th>
                <th width="18%" style="text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rincianDana as $dana)
            <tr>
                <td>{{ \Carbon\Carbon::parse($dana->tanggal_input)->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge {{ $dana->tipe_dana == 'masuk' ? 'badge-masuk' : 'badge-keluar' }}">
                        {{ strtoupper($dana->tipe_dana) }}
                    </span>
                </td>
                <td>
                    <span class="font-bold">{{ $dana->nama_donatur ?? 'Admin' }}</span><br>
                    <small style="color: #718096;">{{ $dana->keterangan_transaksi ?? '-' }}</small>
                </td>
                <td>{{ strtoupper($dana->metode_pembayaran ?? 'Manual') }}</td>
                <td style="text-align: right;" class="font-bold {{ $dana->tipe_dana == 'masuk' ? 'text-green' : 'text-red' }}">
                    {{ $dana->tipe_dana == 'keluar' ? '-' : '' }}Rp {{ number_format($dana->nominal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-container">
        <div class="sig-box">
            <p>Kediri, {{ date('d F Y') }}</p>
            <p class="font-bold">Bendahara Masjid,</p>
            <div class="sig-space"></div>
            <p><strong>( __________________________ )</strong></p>
            <p style="font-size: 8px; color: #718096;">NIP/ID: Admin Masjid Nurul Huda</p>
        </div>
    </div>

    <div class="footer-note">
        Laporan ini sah dan dicetak secara otomatis melalui Sistem Informasi Masjid Nurul Huda pada {{ date('d/m/Y H:i:s') }}.
    </div>

</body>
</html>