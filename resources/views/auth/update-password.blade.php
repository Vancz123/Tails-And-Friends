@extends('layouts.master')

@section('title', 'Alterar palavra-passe')

@section('content')
    <section class="auth-wrap">
        <div class="auth-card">
            <span class="eyebrow">Segurança da conta</span>
            <h1>Alterar palavra-passe</h1>
            <p class="muted">Confirma a palavra-passe atual e define uma nova para a tua conta.</p>
            @include('partials.flash')
            @if ($errors->updatePassword->any())
                <div class="notice notice-error" role="alert">
                    <strong>Não foi possível guardar.</strong>
                    <ul>
                        @foreach ($errors->updatePassword->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('user-password.update') }}" class="form-stack">
                @csrf
                @method('PUT')
                <label for="current_password">Palavra-passe atual</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                <label for="password">Nova palavra-passe</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
                <label for="password_confirmation">Confirmar nova palavra-passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                <button class="button button-primary button-wide" type="submit">Guardar nova palavra-passe</button>
            </form>
        </div>
    </section>
@endsection
