@extends('layouts.master')

@section('title', 'Entrar')

@section('content')
    <section class="auth-wrap">
        <div class="auth-card">
            <span class="eyebrow">Bem-vindo de volta</span>
            <h1>Entrar na conta</h1>
            <p class="muted">Acede ao dashboard e Consulta a nossa equipa!. se fores utilizador podes confirmar onde os meus amigos aparecem!</p>
            @include('partials.flash')
            <form method="POST" action="{{ route('login') }}" class="form-stack">
                @csrf
                <label for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                <label for="password">Palavra-passe</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                <label class="check-row" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    <span>Manter sessão iniciada</span>
                </label>
                <button class="button button-primary button-wide" type="submit">Entrar</button>
            </form>
            <p class="auth-switch">Ainda não tens conta? <a href="{{ route('register') }}">Regista-te com a nossa equipa!</a></p>
        </div>
    </section>
@endsection
