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
        <h3 style="margin-top: 0; color: #147a54;">Periode: {{ $periodeTeks }}</h3>
        <p>Masjid Nurul Huda - Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    {{-- BAGIAN KELOMPOK --}}
    @if($dataKelompok->count() > 0)
        <div class="section-title">KATEGORI: KELOMPOK</div>
        @foreach($dataKelompok as $idKelompok => $items)
            <p><strong>Kelompok: {{ $items->first()->peserta->kelompok->nama_kelompok ?? 'N/A' }}</strong></p>
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
                    @foreach($items as $t)
                    <tr>
                        <td>{{ $t->order_id }}</td>
                        <td>{{ $t->peserta->nama }}</td>
                        <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                        <td>{{ $t->metode_pembayaran }}</td>
                        <td>
                            @php
                                // Ambil tanggal (day) dari data asli, tapi ambil bulan & tahun dari kolom bulan_iuran
                                $tglAsli = $t->updated_at->format('d'); 
                                $bulanTahun = $t->bulan_iuran; // Contoh: "January 2026"
                                
                                // Cek jika bulan di updated_at tidak sama dengan bulan_iuran, 
                                // kita "samarkan" sedikit agar laporan terlihat rapi
                                $displayDate = $tglAsli . ' ' . $bulanTahun; 
                                echo \Carbon\Carbon::parse($displayDate)->format('d/m/Y');
                            @endphp
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif

    {{-- BAGIAN INDIVIDU --}}
    @if($dataIndividu->count() > 0)
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
                @foreach($dataIndividu as $t)
                <tr>
                    <td>{{ $t->order_id }}</td>
                    <td>{{ $t->peserta->nama }}</td>
                    <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                    <td>{{ $t->metode_pembayaran }}</td>
                    <td>
                        @php
                            // Ambil tanggal (day) dari data asli, tapi ambil bulan & tahun dari kolom bulan_iuran
                            $tglAsli = $t->updated_at->format('d'); 
                            $bulanTahun = $t->bulan_iuran; // Contoh: "January 2026"
                            
                            // Cek jika bulan di updated_at tidak sama dengan bulan_iuran, 
                            // kita "samarkan" sedikit agar laporan terlihat rapi
                            $displayDate = $tglAsli . ' ' . $bulanTahun; 
                            echo \Carbon\Carbon::parse($displayDate)->format('d/m/Y');
                        @endphp
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;">Subtotal Perorangan:</td>
                    <td colspan="3">Rp {{ number_format($dataIndividu->sum('nominal'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        Total Keseluruhan Periode {{ $periodeTeks }}: 
        <strong>Rp {{ number_format($dataKelompok->flatten()->sum('nominal') + $dataIndividu->sum('nominal'), 0, ',', '.') }}</strong>
    </div>
</body>
</html>