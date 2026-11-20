<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tails & Friends') · Tails & Friends</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('characters.index') }}">
                <img class="brand-mark" src="{{ asset('images/tails-brand.png') }}" alt="">
                <span>Tails <span class="brand-amp">&</span> Friends</span>
            </a>
            <nav class="main-nav" aria-label="Navegação principal">
                <a href="{{ route('characters.index') }}" @class(['active' => request()->routeIs('characters.*')])>Personagens🦊</a>
                <a href="{{ route('games.index') }}" @class(['active' => request()->routeIs('games.*')])>Jogos 😎</a>
                @auth
                    <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Dashboard 🔎</a>
                @endauth
            </nav>
            <div class="header-account">
                @auth
                    <span class="account-name">{{ auth()->user()->name }}🌐</span>
                    <a class="button button-quiet button-small" href="{{ route('settings.index') }}">⚙️Definições</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="button button-quiet button-small" type="submit"> 👋Terminar sessão</button>
                    </form>
                @else
                    <a class="button button-quiet button-small" href="{{ route('login') }}">Entrar</a>
                    <a class="button button-primary button-small" href="{{ route('register') }}">Registar</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="page-shell">
        @yield('content')
    </main>

    <footer class="site-footer">
        <span>Tails & Friends</span>
        <span>Uma base de dados de personagens e jogos do universo Sonic.</span>
    </footer>
</body>

</html>
