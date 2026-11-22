@extends('layouts.master')

@section('title', 'Criar conta')

@section('content')
    <section class="auth-wrap">
        <div class="auth-card">
            <span class="eyebrow">Junta-te à nossa equipa!</span>
            <h1>Criar conta</h1>
            <p class="muted">Regista-te para verificar os nossos amigos!.</p>
            @include('partials.flash')
            <form method="POST" action="{{ route('register') }}" class="form-stack">
                @csrf
                <label for="name">Nome</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required
                    autofocus>
                <label for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username"
                    required>
                <label for="password">Palavra-passe</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
                <label for="password_confirmation">Confirmar palavra-passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                    required>
                <button class="button button-primary button-wide" type="submit">Criar conta</button>
            </form>
            <p class="auth-switch">Já tens conta? <a href="{{ route('login') }}">Entrar</a></p>
        </div>
    </section>
@endsection
