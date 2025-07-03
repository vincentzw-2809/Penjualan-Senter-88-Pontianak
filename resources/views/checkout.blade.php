@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Informasi Pengiriman</h2>
            <form id="checkout-form">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ auth()->check() ? auth()->user()->email : '' }}" required>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Kota Asal</label>
                    <select name="origin" id="origin" class="form-control" required>
                        <option value="">-- Pilih Kota Asal --</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city['id'] }}">{{ $city['type'] }} {{ $city['city_name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Kota Tujuan</label>
                    <select name="destination" id="destination" class="form-control" required>
                        <option value="">-- Pilih Kota Tujuan --</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city['id'] }}">{{ $city['type'] }} {{ $city['city_name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Berat Paket (gram)</label>
                    <input type="number" name="weight" id="weight" class="form-control" value="1000" required>
                </div>
                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Kurir</label>
                    <select name="courier" id="courier" class="form-control" required>
                        <option value="jne">JNE</option>
                        <option value="tiki">TIKI</option>
                        <option value="pos">POS Indonesia</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Layanan Ongkir</label>
                    <select name="shipping_cost" id="shipping_cost" class="form-control" required>
                        <option value="">-- Pilih Layanan --</option>
                    </select>
                </div>
                <input type="hidden" name="shipping_service" id="shipping_service">
                <button type="button" id="pay-button" class="btn btn-success btn-block">Bayar Sekarang</button>
            </form>
        </div>

        <div class="col-md-6">
            <h2>Ringkasan Belanja</h2>
            <ul class="list-group">
                @foreach (Cart::content() as $item)
                    <li class="list-group-item">
                        {{ $item->name }} x {{ $item->qty }}
                        <span class="float-right">Rp{{ number_format($item->subtotal) }}</span>
                    </li>
                @endforeach
            </ul>
            <hr>
            <p>Subtotal: <strong>Rp{{ number_format($subtotal) }}</strong></p>
            <p>Tax (3%): <strong>Rp{{ number_format($tax) }}</strong></p>
            <p>Total Produk: <strong id="product-total">Rp{{ number_format($total) }}</strong></p>
            <p id="ongkir-preview">Ongkir: <strong>Rp0</strong></p>
            <p id="grand-total">Total Bayar: <strong>Rp{{ number_format($total) }}</strong></p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    const subtotal = {{ $subtotal }};
    const tax = {{ $tax }};
    let shippingCost = 0;

    function calculateShipping() {
        const origin = document.getElementById('origin').value;
        const destination = document.getElementById('destination').value;
        const courier = document.getElementById('courier').value;

        if (!origin || !destination || !courier) return;

        fetch('/checkout/shipping-cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ origin, destination, courier })
        })
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('shipping_cost');
            select.innerHTML = data.services.map(service => {
                const cost = service.cost[0].value;
                return `<option value="${cost}" data-service="${service.service}">${service.service} - Rp${cost}</option>`;
            }).join('');
        });
    }

    ['origin', 'destination', 'courier'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', calculateShipping);
    });

    document.getElementById('shipping_cost').addEventListener('change', function () {
        shippingCost = parseInt(this.value);
        const service = this.options[this.selectedIndex].dataset.service;
        document.getElementById('shipping_service').value = service;

        const productTotal = subtotal + tax;
        const totalBayar = productTotal + shippingCost;

        document.getElementById('ongkir-preview').innerHTML = 'Ongkir: <strong>Rp' + shippingCost + '</strong>';
        document.getElementById('grand-total').innerHTML = 'Total Bayar: <strong>Rp' + totalBayar.toLocaleString() + '</strong>';
    });

    document.getElementById('pay-button').addEventListener('click', function () {
        const form = document.getElementById('checkout-form');
        const formData = new FormData(form);

        fetch("{{ route('checkout.store') }}", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        window.location.href = "/thankyou";
                    },
                    onPending: function(result){
                        alert("Pembayaran pending");
                    },
                    onError: function(result){
                        alert("Pembayaran gagal");
                    }
                });
            } else {
                alert("Gagal mendapatkan token pembayaran.");
            }
        })
        .catch(err => {
            alert("Terjadi kesalahan: " + err.message);
        });
    });
</script>
@endsection
