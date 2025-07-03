@extends('layouts.app')
@section('title', 'Terima Kasih')

@section('content')
<div class="container text-center py-5">
    <div class="card shadow-lg p-4 mx-auto" style="max-width: 600px;">
        <h1 class="text-success mb-4">
            <i class="fas fa-check-circle fa-3x"></i><br>
            Terima Kasih!
        </h1>
        <p class="lead">Pesanan kamu sudah kami terima dan sedang diproses.</p>

        @if(session('success'))
            <div class="alert alert-success mt-4">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('shop.index') }}" class="btn btn-primary mt-4">
            Lanjut Belanja
        </a>
    </div>
</div>
@endsection
