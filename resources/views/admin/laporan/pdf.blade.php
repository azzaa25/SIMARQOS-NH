<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Realisasi Qurban - {{ $tahunSelected ?? 'Semua Tahun' }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; margin: 0; padding: 0; }
        
        /* Header Style */
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #147a54; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #147a54; font-size: 18pt; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0; color: #666; font-style: italic; font-size: 9pt; }

        /* Summary Box - Disesuaikan agar sama dengan Dashboard Web */
        .summary-wrapper { margin-bottom: 30px; }
        .summary-box { width: 100%; background-color: #f4fbf8; border: 1px solid #cde7db; border-radius: 10px; padding: 15px; }
        .summary-box table { width: 100%; border: none; }
        .summary-box td { padding: 5px; border: none; font-size: 10pt; }
        .label { font-weight: bold; color: #555; width: 250px; }
        .value { text-align: left; font-weight: bold; color: #333; }
        .highlight { color: #147a54; font-size: 12pt; border-top: 1px solid #cde7db !important; }

        /* Section Tahun */
        .tahun-title { 
            background: #147a54; color: white; padding: 8px 15px; 
            border-radius: 5px; margin-top: 30px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 1px;
        }

        /* Table Style */
        .skema-section { margin-top: 15px; margin-bottom: 20px; }
        .skema-badge { 
            background-color: #e8f5e9; color: #2e7d32; padding: 4px 12px; 
            border-radius: 4px; font-weight: bold; font-size: 9pt; 
            margin-bottom: 8px; display: inline-block; border: 1px solid #c8e6c9;
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #f2f2f2; color: #147a54; padding: 8px; border: 1px solid #ddd; font-size: 9pt; text-align: center; }
        td { padding: 8px; border: 1px solid #ddd; vertical-align: middle; }
        
        .row-even { background-color: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Subtotal Row */
        .subtotal { background-color: #f0fdf4; font-weight: bold; color: #147a54; }
        
        /* Total Per Tahun Box */
        .total-tahun-box { 
            text-align: right; background: #f8fafc; padding: 10px; 
            border: 1px solid #cbd5e1; border-radius: 5px; 
            margin-top: 10px; font-weight: bold; color: #1e293b;
        }

        /* Footer */
        .footer { margin-top: 50px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { border: none; text-align: center; width: 50%; }
        .signature-space { height: 70px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Realisasi Dana Qurban</h1>
        <p>Masjid Nurul Huda Kediri | Periode: {{ $tahunSelected ?? 'Semua Tahun' }}</p>
        <p style="font-size: 7pt; font-style: normal;">ID Dokumen: QRN-{{ time() }} | Dicetak: {{ date('d M Y H:i') }} WIB</p>
    </div>

    <div class="summary-wrapper">
        <div class="summary-box">
            <table>
                <tr>
                    <td class="label">Total Iuran Terkumpul (Global)</td>
                    <td class="value">: Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    {{-- Diubah agar jelas bahwa ini nominal yang terdaftar di tabel bawah --}}
                    <td class="label">Dana Terealisasi Terlampir ({{ $tahunSelected ?? 'Semua' }})</td>
                    <td class="value" style="color: #b91c1c;">: - Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    {{-- Sisa Saldo Kas Masjid (Selisih Global - Semua Pengeluaran) --}}
                    <td class="label highlight">Sisa Saldo Kas Masjid (Keseluruhan)</td>
                    <td class="value highlight">: Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- LOOP PER TAHUN --}}
    @forelse($dataGrouped as $tahun => $skemas)
        <div class="tahun-title">REKAPITULASI REALISASI TAHUN {{ $tahun }}</div>

        {{-- LOOP PER SKEMA --}}
        @foreach($skemas as $namaSkema => $items)
            <div class="skema-section">
                <div class="skema-badge">SKEMA: {{ strtoupper($namaSkema) }}</div>
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="35%">Nama Pemenang</th>
                            <th width="20%">Kategori</th>
                            <th width="25%">Nominal Realisasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $p)
                        <tr class="{{ $index % 2 == 0 ? '' : 'row-even' }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($p->tanggal_pengeluaran)->format('d/m/Y') }}</td>
                            <td>
                                <span class="font-bold">{{ strtoupper($p->undian->peserta->nama ?? 'N/A') }}</span><br>
                                <span style="font-size: 7pt; color: #777;">ID: {{ $p->order_id }}</span>
                            </td>
                            <td class="text-center">
                                {{-- Menampilkan informasi kelompok jika ada --}}
                                @if($p->undian->peserta->id_kelompok)
                                    <span style="color: #2563eb;">Kelompok: {{ $p->undian->peserta->kelompok->kode_kelompok }}</span>
                                @else
                                    <span style="color: #64748b;">Perorangan</span>
                                @endif
                            </td>
                            {{-- Nominal per orang (hasil bagi jika kelompok) --}}
                            <td class="text-right font-bold">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="subtotal">
                            <td colspan="4" class="text-right">SUBTOTAL {{ strtoupper($namaSkema) }} :</td>
                            <td class="text-right">Rp {{ number_format($items->sum('nominal'), 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        {{-- TOTAL PER TAHUN --}}
        <div class="total-tahun-box">
            TOTAL REALISASI TAHUN {{ $tahun }}: Rp 
            @php
                $totalTahun = 0;
                foreach($skemas as $s) { $totalTahun += $s->sum('nominal'); }
            @endphp
            {{ number_format($totalTahun, 0, ',', '.') }}
        </div>
        
    @empty
        <div style="text-align: center; padding: 50px; border: 1px dashed #ddd; border-radius: 10px; margin-top: 20px;">
            <p style="color: #999; font-weight: bold; text-transform: uppercase;">Tidak ada data realisasi yang ditemukan.</p>
        </div>
    @endforelse

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Ketua Takmir Masjid</strong>
                    <div class="signature-space"></div>
                    ( ................................... )
                </td>
                <td>
                    Kediri, {{ date('d F Y') }}<br>
                    <strong>Bendahara Qurban</strong>
                    <div class="signature-space"></div>
                    ( ................................... )
                </td>
            </tr>
        </table>
    </div>

</body>
</html>