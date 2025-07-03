@extends('voyager::master')

@section('content')
<div class="container-fluid">
    <h3 class="page-title">Laporan Penjualan 88 kite</h3>
    
    <form method="GET" class="form-inline mb-4">
        <input type="date" name="start_date" class="form-control mr-2" value="{{ request('start_date') }}">
        <input type="date" name="end_date" class="form-control mr-2" value="{{ request('end_date') }}">
        <button class="btn btn-primary">Filter</button>
        <a href="{{ route('voyager.reports.pdf', request()->all()) }}" class="btn btn-danger ml-2">Cetak PDF</a>
    </form>

    <table class="table table-bordered">
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
                <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Total Penjualan: Rp{{ number_format($total, 0, ',', '.') }}</h4>
</div>
@endsection
