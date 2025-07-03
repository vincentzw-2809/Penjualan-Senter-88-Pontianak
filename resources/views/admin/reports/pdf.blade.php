<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        h3 { text-align: center; }
    </style>
</head>
<body>
    <h3>Laporan Penjualan</h3>
    <p>
        Tanggal: {{ $request->start_date ?? '-' }} s.d. {{ $request->end_date ?? '-' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No Order</th>
                <th>Nama Pembeli</th>
                <th>Kota / Provinsi</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->billing_name }}</td>
                <td>{{ $order->billing_city }}, {{ $order->billing_province }}</td>
                <td>Rp{{ number_format($order->billing_total, 0, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <p><strong>Total Penjualan:</strong> Rp{{ number_format($total, 0, ',', '.') }}</p>
</body>
</html>
