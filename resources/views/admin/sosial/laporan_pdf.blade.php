<!DOCTYPE html>
<html>
<head>
    <title>Laporan {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a202c;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }
        
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

        /* Status Banner */
        .status-banner {
            padding: 8px;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-rencana { background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; }
        .status-berlangsung { background: #fffaf0; color: #c05621; border: 1px solid #feebc8; }
        .status-selesai { background: #f0fff4; color: #2f855a; border: 1px solid #c6f6d5; }

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
        }
        .card-masuk { border-left: 4px solid #10b981; }
        .card-keluar { border-left: 4px solid #ef4444; }
        .card-target { border-left: 4px solid #3182ce; }
        
        .card-label {
            font-size: 8px;
            color: #718096;
            text-transform: uppercase;
            font-weight: bold;
        }
        .card-value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }

        .section-title {
            color: #064e3b;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 3px solid #064e3b;
            padding-left: 8px;
        }

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
        .sig-space { height: 60px; }

        .footer-note {
            margin-top: 50px;
            font-size: 8px;
            color: #718096;
            border-top: 1px solid #edf2f7;
            padding-top: 5px;
            text-align: center;
            clear: both;
        }

        .text-green { color: #065f46; }
        .text-red { color: #991b1b; }
        .text-blue { color: #2b6cb0; }
    </style>
</head>
<body>
    @php
        \Carbon\Carbon::setLocale('id');
    @endphp

    <div class="header">
        <h1>Masjid Nurul Huda</h1>
        <p>Laporan Pertanggungjawaban Dana Kegiatan Sosial</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    {{-- Banner Status Dinamis --}}
    <div class="status-banner status-{{ $kegiatan->status_kegiatan }}">
        @if($kegiatan->status_kegiatan == 'rencana')
            Status Agenda: Tahap Perencanaan & Penggalangan Dana
        @elseif($kegiatan->status_kegiatan == 'berlangsung')
            Status Agenda: Sedang Dilaksanakan
        @else
            Status Agenda: Telah Selesai / Terlaksana
        @endif
    </div>

    <div class="section-title">Informasi Agenda</div>
    <table style="border: none; width: 100%; margin-bottom: 20px; border-collapse: collapse;">
        <tr>
            <td style="border: none; width: 100px; padding: 2px 0;">Nama Kegiatan</td>
            <td style="border: none; padding: 2px 0;">: <strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 0;">Kategori</td>
            <td style="border: none; padding: 2px 0;">: {{ $kegiatan->kategori->nama_kategori ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 0;">Rencana Waktu</td>
            <td style="border: none; padding: 2px 0;">: {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 0;">Lokasi Agenda</td>
            <td style="border: none; padding: 2px 0;">: {{ $kegiatan->lokasi }}</td>
        </tr>
    </table>

    <table class="stats-table">
        <tr>
            <td class="card card-target">
                <div class="card-label">Target Donasi</div>
                <div class="card-value text-blue">Rp {{ number_format($kegiatan->target_donasi, 0, ',', '.') }}</div>
            </td>
            <td class="card card-masuk">
                <div class="card-label">Dana Terkumpul</div>
                <div class="card-value text-green">Rp {{ number_format($totalDanaMasuk, 0, ',', '.') }}</div>
            </td>
            <td class="card card-keluar">
                <div class="card-label">Penyaluran Dana</div>
                <div class="card-value text-red">Rp {{ number_format($totalDanaKeluar, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- Info Khusus jika masih Rencana --}}
    @if($kegiatan->status_kegiatan == 'rencana')
        @php $kurang = $kegiatan->target_donasi - $totalDanaMasuk; @endphp
        <div style="background: #f7faf2; padding: 10px; border-radius: 8px; border: 1px solid #d4e3b5; margin-bottom: 20px;">
            <table width="100%" style="border: none;">
                <tr>
                    <td style="border: none; font-size: 10px;">
                        <strong>Catatan Perencanaan:</strong><br>
                        Saat ini dana terkumpul telah mencapai <strong>{{ round(($totalDanaMasuk / max($kegiatan->target_donasi, 1)) * 100) }}%</strong> dari target. 
                        @if($kurang > 0)
                            Dibutuhkan tambahan sebesar <strong>Rp {{ number_format($kurang, 0, ',', '.') }}</strong> untuk mencapai target donasi.
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div class="section-title">Rincian Transaksi Dana</div>
    <table class="main-table">
        <thead>
            <tr>
                <th width="18%">Waktu Transaksi</th>
                <th width="12%">Tipe</th>
                <th>Keterangan / Sumber Dana</th>
                <th width="18%" style="text-align: right;">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rincianDana as $dana)
            <tr>
                <td>{{ \Carbon\Carbon::parse($dana->tanggal_input)->translatedFormat('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge {{ $dana->tipe_dana == 'masuk' ? 'badge-masuk' : 'badge-keluar' }}">
                        {{ strtoupper($dana->tipe_dana) }}
                    </span>
                </td>
                <td>
                    <span style="font-weight: bold;">{{ $dana->nama_donatur ?? 'Kas Masjid' }}</span><br>
                    <small style="color: #718096;">{{ $dana->keterangan_transaksi ?? '-' }}</small>
                </td>
                <td style="text-align: right; font-weight: bold;" class="{{ $dana->tipe_dana == 'masuk' ? 'text-green' : 'text-red' }}">
                    {{ $dana->tipe_dana == 'keluar' ? '-' : '' }}{{ number_format($dana->nominal, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #a0aec0; padding: 20px;">Belum ada transaksi tercatat untuk kegiatan ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if($rincianDana->count() > 0)
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold; background: #f8fafc;">SISA SALDO SAAT INI</td>
                <td style="text-align: right; font-weight: bold; background: #f8fafc;" class="text-blue">
                    Rp {{ number_format($totalDanaMasuk - $totalDanaKeluar, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="signature-container">
        <div class="sig-box">
            <p>Kediri, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="font-weight: bold;">Bendahara Masjid,</p>
            <div class="sig-space"></div>
            <p><strong>( __________________________ )</strong></p>
            <p style="font-size: 8px; color: #718096;">Sistem Informasi Masjid Nurul Huda</p>
        </div>
    </div>

    <div class="footer-note">
        Laporan ini sah dan dicetak secara otomatis pada {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i:s') }}.
    </div>

</body>
</html>