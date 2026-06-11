<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Monitoring & Realisasi Qurban - {{ $tahunSelected ?? 'Semua Tahun' }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        
        /* Header Style */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #147a54; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #147a54; font-size: 16pt; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 3px 0; color: #666; font-style: italic; font-size: 8pt; }

        /* Summary Box */
        .summary-box { width: 100%; background-color: #f4fbf8; border: 1px solid #cde7db; border-radius: 8px; padding: 12px; margin-bottom: 20px; }
        .summary-box table { width: 100%; border: none; }
        .summary-box td { padding: 3px; border: none; }
        .label { font-weight: bold; color: #555; width: 220px; }
        .value { text-align: left; font-weight: bold; color: #333; }
        .highlight { color: #147a54; font-size: 11pt; border-top: 1px solid #cde7db !important; padding-top: 8px !important; }

        /* Section Title */
        .section-title { 
            background: #147a54; color: white; padding: 6px 12px; 
            border-radius: 4px; margin-top: 25px; font-weight: bold;
            text-transform: uppercase; font-size: 10pt;
        }

        /* Table Style */
        .skema-badge { 
            background-color: #e8f5e9; color: #2e7d32; padding: 3px 10px; 
            border-radius: 4px; font-weight: bold; font-size: 8pt; 
            margin: 15px 0 5px 0; display: inline-block; border: 1px solid #c8e6c9;
        }
        
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f2f2f2; color: #147a54; padding: 8px; border: 1px solid #ddd; font-size: 8pt; text-transform: uppercase; }
        td { padding: 8px; border: 1px solid #ddd; vertical-align: middle; }
        
        .row-even { background-color: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Status Badge */
        .badge-menang { color: #b91c1c; font-weight: bold; text-transform: uppercase; font-size: 7pt; }
        .badge-belum { color: #64748b; font-weight: bold; text-transform: uppercase; font-size: 7pt; }

        /* Subtotal Row */
        .subtotal { background-color: #f0fdf4; font-weight: bold; color: #147a54; font-size: 8pt; }
        
        /* Total Per Tahun Box */
        .total-tahun-box { 
            text-align: right; background: #f8fafc; padding: 10px; 
            border: 1px solid #cbd5e1; border-radius: 5px; 
            margin-top: 10px; font-weight: bold; color: #1e293b; font-size: 9pt;
        }

        .footer { margin-top: 40px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { border: none; text-align: center; width: 50%; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Monitoring & Realisasi Arisan Qurban</h1>
        <p>Masjid Nurul Huda Kediri | Filter: {{ $filterStatus == 'pemenang' ? 'Sudah Menang' : ($filterStatus == 'belum' ? 'Belum Menang' : 'Semua Peserta') }}</p>
        <p>Periode Pelaksanaan: {{ $tahunSelected ?? 'Semua Tahun' }}</p>
        <p style="font-size: 7pt; font-style: normal;">Dicetak pada: {{ date('d M Y H:i') }} WIB</p>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td class="label">Total Iuran Terkumpul (Sesuai List)</td>
                <td class="value">: Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Dana Terealisasi (Sesuai List)</td>
                <td class="value" style="color: #b91c1c;">: - Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label highlight">Sisa Saldo Kas Masjid (Global)</td>
                <td class="value highlight">: Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- LOOP PER TAHUN PELAKSANAAN --}}
    @forelse($dataGrouped as $tahun => $skemas)
        <div class="section-title">
            {{ $tahun == 'Belum Undian' ? 'Monitoring Peserta (Belum Mendapatkan Undian)' : 'Laporan Pelaksanaan Tahun ' . $tahun }}
        </div>

        @foreach($skemas as $namaSkema => $items)
            <div class="skema-badge">SKEMA: {{ strtoupper($namaSkema) }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Informasi Peserta</th>
                        <th width="20%">Progres Iuran</th>
                        <th width="20%">Status Undian</th>
                        <th width="25%">Nominal (Realisasi/Saldo)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $p)
                        @php
                            $isMenang = $p->pengeluaranArisan != null;
                            $tenor = $p->skemaArisan->durasi_bulan ?? 1;
                            $lunas = $p->transaksi->count();
                            $nominalTampil = $isMenang ? $p->pengeluaranArisan->nominal : $p->transaksi->sum('nominal');
                        @endphp
                        <tr class="{{ $index % 2 == 0 ? '' : 'row-even' }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <span class="font-bold">{{ strtoupper($p->nama) }}</span><br>
                                <span style="font-size: 7pt; color: #777;">
                                    {{ $p->kelompok->nama_kelompok ?? 'PERORANGAN' }} | 
                                    {{ $p->user->status == 'nonaktif' ? 'SELESAI' : 'AKTIF' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="font-bold">{{ $lunas }} / {{ $tenor }} Bulan</span><br>
                                <span style="font-size: 7pt; color: #666;">({{ round(($lunas/$tenor)*100) }}%)</span>
                            </td>
                            <td class="text-center">
                                @if($isMenang)
                                    <span class="badge-menang">SUDAH MENANG<br>({{ $p->undian->tahun_pelaksanaan }})</span>
                                @else
                                    <span class="badge-belum">BELUM MENANG</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="font-bold" style="color: {{ $isMenang ? '#b91c1c' : '#147a54' }}">
                                    Rp {{ number_format($nominalTampil, 0, ',', '.') }}
                                </span><br>
                                <span style="font-size: 7pt; color: #888; font-style: italic;">
                                    {{ $isMenang ? 'Dana Realisasi' : 'Tabungan Qurban' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    {{-- SUBTOTAL PER SKEMA --}}
                    <tr class="subtotal">
                        <td colspan="4" class="text-right">SUBTOTAL {{ strtoupper($namaSkema) }} :</td>
                        <td class="text-right">
                            @php
                                $subtotal = $items->reduce(function ($carry, $p) {
                                    return $carry + ($p->pengeluaranArisan ? $p->pengeluaranArisan->nominal : $p->transaksi->sum('nominal'));
                                }, 0);
                            @endphp
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endforeach

        {{-- TOTAL PER TAHUN / SECTION --}}
        <div class="total-tahun-box">
            TOTAL {{ $tahun == 'Belum Undian' ? 'TABUNGAN QURBAN' : 'REALISASI TAHUN ' . $tahun }} : Rp 
            @php
                $totalSection = 0;
                foreach($skemas as $itemsSkema) {
                    $totalSection += $itemsSkema->reduce(function ($carry, $p) {
                        return $carry + ($p->pengeluaranArisan ? $p->pengeluaranArisan->nominal : $p->transaksi->sum('nominal'));
                    }, 0);
                }
            @endphp
            {{ number_format($totalSection, 0, ',', '.') }}
        </div>

    @empty
        <div style="text-align: center; padding: 30px; border: 1px dashed #ddd; margin-top: 20px;">
            <p style="color: #999;">Tidak ada data peserta ditemukan untuk kriteria ini.</p>
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