<nav class="navbar navbar-expand-lg fixed-top navbar-dark" style="background-color: #fca878; font-family: 'Poppins', sans-serif;">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            Senter Kepala 88
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            {{-- Menu Utama --}}
            {{ menu('main', 'partials.menu.main') }}

            {{-- Pencarian --}}
            <form class="form-inline ml-lg-3 my-2 my-lg-0" onsubmit="return searchProduct(event)">
                <input id="search" name="search" type="search" style="width: 400px;" class="form-control custom-border"
                    placeholder="Cari produk..." aria-label="Search" required>
            </form>
        </div>

        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ml-auto">
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                @if (auth()->user()->can('browse_admin'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/admin') }}">Admin Panel</a>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle mr-2" width="30" height="30" alt="avatar">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" class="rounded-circle mr-2" width="30" height="30" alt="default avatar">
                        @endif
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>

<div id="cont">
    <ul style="height:4em"></ul>
</div>

<script>
    function searchProduct(e) {
        e.preventDefault();
        const keyword = document.getElementById('search').value.trim();
        if (keyword.length >= 3) {
            window.location.href = '/shop/search/' + encodeURIComponent(keyword);
        } else {
            alert('Minimum query length is 3 karakter.');
        }
    }
</script>
