<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- ✅ Tambahkan baris ini agar Snap Midtrans tidak diblokir CSP --}}
    <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https://app.sandbox.midtrans.com">

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    {{-- Styles --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    {{-- FontAwesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @yield('stylesheet')
</head>

<body style="font-family: 'Poppins', sans-serif;">
    <div id="app">
        {{-- Navbar --}}
        @include('partials.nav')

        {{-- Content --}}
        <div class="container-fluid px-3 py-2" style="margin-top: {{ Request::is('/') ? '-1.3em' : '2em' }}">
            @include('partials.session')
            @include('partials.errors')
            @yield('content')
        </div>

        {{-- Footer --}}
        @include('partials.footer')
    </div>

    {{-- Laravel App JS --}}
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- AOS animation (optional) --}}
    <script>
        $(function () {
            if (typeof AOS !== 'undefined') {
                AOS.init();
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
