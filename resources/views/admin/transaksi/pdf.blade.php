<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Arisan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #147a54; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN TRANSAKSI ARISAN QURBAN</h2>
        <p>Masjid Nurul Huda - Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Order</th>
                <th>Nama Peserta</th>
                <th>Nominal</th>
                <th>Metode</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $t)
            <tr>
                <td>{{ $t->order_id }}</td>
                <td>{{ $t->peserta->nama }}</td>
                <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                <td>{{ $t->metode_pembayaran }}</td>
                <td>{{ $t->updated_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>