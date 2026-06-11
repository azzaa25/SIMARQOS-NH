<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Arisan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; color: #147a54; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #147a54; padding-bottom: 10px; }
        .section-title { background-color: #147a54; color: white; padding: 5px 10px; font-weight: bold; margin-bottom: 10px; border-radius: 3px; }
        .footer { text-align: right; margin-top: 20px; font-style: italic; font-size: 10px; }
        .total-row { background-color: #fafafa; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin-bottom: 5px;">LAPORAN TRANSAKSI ARISAN QURBAN</h2>
        <h3 style="margin-top: 0; color: #147a54;">
            Periode: 
            @php
                // Memisahkan teks jika ada tambahan "- Skema: ..."
                $parts = explode(' - ', $periodeTeks);
                $bulanTahunInggris = $parts[0]; // Bagian "March 2026"
                $tambahanSkema = isset($parts[1]) ? ' - ' . $parts[1] : '';

                try {
                    // Konversi teks bulan Inggris ke Indonesia
                    $bulanIndo = \Carbon\Carbon::createFromFormat('F Y', $bulanTahunInggris)
                                    ->locale('id')
                                    ->translatedFormat('F Y');
                    echo $bulanIndo . $tambahanSkema;
                } catch (\Exception $e) {
                    // Fallback jika format tidak sesuai (misal sudah Indonesia atau "Semua Periode")
                    echo $periodeTeks;
                }
            @endphp
        </h3>
        <p>Masjid Nurul Huda - Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    @php $grandTotalKeseluruhan = 0; @endphp

    {{-- ══════════════════════════════════════════
         BAGIAN KELOMPOK
         ══════════════════════════════════════════ --}}
    @if($dataKelompok->count() > 0)
        <div class="section-title">KATEGORI: KELOMPOK</div>
        @foreach($dataKelompok as $idKelompok => $items)
            @php 
                // Filter hanya yang sukses untuk memastikan data bulan lain tidak bocor
                $validItems = $items->where('status_pembayaran', 'sukses');
            @endphp

            @if($validItems->count() > 0)
            <p><strong>Kelompok: {{ $validItems->first()->peserta->kelompok->nama_kelompok ?? 'N/A' }}</strong></p>
            <table>
                <thead>
                    <tr>
                        <th width="20%">ID Order</th>
                        <th width="35%">Nama Peserta</th>
                        <th width="15%">Nominal</th>
                        <th width="15%">Metode</th>
                        <th width="15%">Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotalKelompok = 0; @endphp
                    @foreach($validItems as $t)
                        @php 
                            $subtotalKelompok += $t->nominal; 
                            $grandTotalKeseluruhan += $t->nominal;
                        @endphp
                        <tr>
                            <td>{{ $t->order_id }}</td>
                            <td>{{ $t->peserta->nama }}</td>
                            <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                            {{-- 🛠️ PERBAIKAN: Jika metode kosong (seperti Zakiya), tampilkan 'Tunai' --}}
                            <td>{{ $t->metode_pembayaran ?: 'Tunai' }}</td>
                            <td>
                                @php
                                    $tglAsli = $t->updated_at->format('d'); 
                                    $bulanTahun = $t->bulan_iuran; 
                                    $displayDate = $tglAsli . ' ' . $bulanTahun; 
                                    try {
                                        echo \Carbon\Carbon::createFromFormat('d F Y', $displayDate)->format('d/m/Y');
                                    } catch(\Exception $e) {
                                        echo $t->updated_at->format('d/m/Y');
                                    }
                                @endphp
                            </td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; color: #147a54;">Total {{ $validItems->first()->peserta->kelompok->nama_kelompok ?? '' }}:</td>
                        <td colspan="3" style="color: #147a54;"><strong>Rp {{ number_format($subtotalKelompok, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
            @endif
        @endforeach
    @endif

    {{-- ══════════════════════════════════════════
         BAGIAN INDIVIDU
         ══════════════════════════════════════════ --}}
    @php 
        $validIndividu = $dataIndividu->where('status_pembayaran', 'sukses');
    @endphp

    @if($validIndividu->count() > 0)
        <div class="section-title">KATEGORI: PERORANGAN / INDIVIDU</div>
        <table>
            <thead>
                <tr>
                    <th width="20%">ID Order</th>
                    <th width="35%">Nama Peserta</th>
                    <th width="15%">Nominal</th>
                    <th width="15%">Metode</th>
                    <th width="15%">Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotalIndividu = 0; @endphp
                @foreach($validIndividu as $t)
                    @php 
                        $subtotalIndividu += $t->nominal; 
                        $grandTotalKeseluruhan += $t->nominal;
                    @endphp
                    <tr>
                        <td>{{ $t->order_id }}</td>
                        <td>{{ $t->peserta->nama }}</td>
                        <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                        <td>{{ $t->metode_pembayaran ?: 'Tunai' }}</td>
                        <td>
                            @php
                                $tglAsli = $t->updated_at->format('d'); 
                                $bulanTahun = $t->bulan_iuran; 
                                $displayDate = $tglAsli . ' ' . $bulanTahun; 
                                try {
                                    echo \Carbon\Carbon::createFromFormat('d F Y', $displayDate)->format('d/m/Y');
                                } catch(\Exception $e) {
                                    echo $t->updated_at->format('d/m/Y');
                                }
                            @endphp
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="text-align: right; color: #147a54;">Subtotal Perorangan:</td>
                    <td colspan="3" style="color: #147a54;"><strong>Rp {{ number_format($subtotalIndividu, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        Total Pendapatan Terfilter: 
        <strong>Rp {{ number_format($grandTotalKeseluruhan, 0, ',', '.') }}</strong>
    </div>
</body>
</html>