@extends('layouts.app')
@section('title', 'Cart')
@section('content')

<!-- start page content -->
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            @if (Cart::instance('default')->count() > 0)
            <h3 class="lead mt-4">{{ Cart::instance('default')->count() }} item di keranjang</h3>
            <table class="table table-responsive">
                <tbody>
                    @foreach (Cart::instance('default')->content() as $item)
                        <tr>
                            <td>
                                <a href="{{ route('shop.show', $item->model->slug) }}">
                                    <img src="{{ productImage($item->model->image) }}" height="100px" width="100px">
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('shop.show', $item->model->slug) }}" class="text-decoration-none">
                                    <h3 class="lead light-text">{{ $item->model->name }}</h3>
                                    <p class="light-text">{{ $item->model->details }}</p>
                                </a>
                            </td>
                            <td>
                                <form action="{{ route('cart.destroy', [$item->rowId, 'default']) }}" method="POST" id="delete-item-{{ $item->rowId }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <form action="{{ route('cart.save-later', $item->rowId) }}" method="POST" id="save-later-{{ $item->rowId }}">
                                    @csrf
                                </form>
                                <button class="cart-option btn btn-danger btn-sm custom-border" onclick="document.getElementById('delete-item-{{ $item->rowId }}').submit();">
                                    Hapus
                                </button>
                                <button class="cart-option btn btn-success btn-sm custom-border" onclick="document.getElementById('save-later-{{ $item->rowId }}').submit();">
                                    Simpan Nanti
                                </button>
                            </td>
                            <td>
                                <select class='quantity' data-id='{{ $item->rowId }}' data-productQuantity='{{ $item->model->quantity }}'>
                                    @for ($i = 1; $i < 10; $i++)
                                        <option value="{{ $i }}" {{ $item->qty == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
            <div class="summary">
                <div class="row">
                    <div class="col-md-8">
                        <p class="light-text">
                            Cek kembali produk yang telah Anda pilih. Anda masih bisa menghapus, menyimpan, atau mengubah kuantitas sebelum checkout.
                        </p>
                    </div>
                    <div class="col-md-3 offset-md-1">
                        <p class="text-right light-text">Subtotal: Rp{{ number_format(Cart::subtotal(), 0, ',', '.') }}</p>
                        <p class="text-right light-text">Tax (21%): Rp{{ number_format(Cart::tax(), 0, ',', '.') }}</p>
                        <p class="text-right font-weight-bold">Total: Rp{{ number_format(Cart::total(), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="cart-actions">
                <a class="btn custom-border-n" href="{{ route('shop.index') }}">Lanjut Belanja</a>
                <a class="float-right btn btn-success custom-border-n" href="{{ route('checkout.index') }}">
                    Lanjut ke Checkout
                </a>
            </div>
            @else
            <div class="alert alert-info">
                <h4 class="lead">Keranjang kosong. <a class="btn custom-border-n" href="{{ route('shop.index') }}">Belanja Sekarang</a></h4>
            </div>
            @endif
            <hr>
            @if (Cart::instance('saveForLater')->count() > 0)
                <h3 class="lead">{{ Cart::instance('saveForLater')->count() }} item disimpan untuk nanti</h3>
                <table class="table table-responsive">
                    <tbody>
                        @foreach (Cart::instance('saveForLater')->content() as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('shop.show', $item->model->slug) }}">
                                        <img src="{{ productImage($item->model->image) }}" height="100px" width="100px">
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('shop.show', $item->model->slug) }}" class="text-decoration-none">
                                        <h3 class="lead light-text">{{ $item->model->name }}</h3>
                                        <p class="light-text">{{ $item->model->details }}</p>
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('cart.destroy', [$item->rowId, 'saveForLater']) }}" method="POST" id="delete-form-{{ $item->rowId }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <form action="{{ route('cart.add-to-cart', $item->rowId) }}" method="POST" id="add-form-{{ $item->rowId }}">
                                        @csrf
                                    </form>
                                    <button class="cart-option btn btn-danger btn-sm custom-border" onclick="document.getElementById('delete-form-{{ $item->rowId }}').submit();">
                                        Hapus
                                    </button>
                                    <button class="cart-option btn btn-success btn-sm custom-border" onclick="document.getElementById('add-form-{{ $item->rowId }}').submit();">
                                        Tambahkan ke Keranjang
                                    </button>
                                </td>
                                <td>Rp{{ number_format($item->model->price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-primary mt-4">
                    <li>Tidak ada item yang disimpan untuk nanti.</li>
                </div>
            @endif
        </div>
    </div>
</div>
@include('partials.might-like')
<!-- end page content -->

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.quantity').on('change', function() {
            const id = this.getAttribute('data-id');
            const productQuantity = this.getAttribute('data-productQuantity');
            axios.patch('/cart/' + id, {
                quantity: this.value,
                productQuantity: productQuantity
            })
            .then(response => {
                window.location.href = '{{ route('cart.index') }}';
            }).catch(error => {
                window.location.href = '{{ route('cart.index') }}';
            });
        });
    });
</script>
@endsection
