@extends('layouts.master')

@section('title', 'Definições da conta')

@section('content')
    <section class="auth-wrap">
        <div class="auth-card settings-card">
            <span class="eyebrow">Área pessoal</span>
            <h1>Definições da conta</h1>
            <p class="muted">Gere a tua palavra-passe e a tua conta.</p>

            @include('partials.flash')

            <!-- Password Form -->
            <form method="POST" action="{{ route('user-password.update') }}" class="form-stack">
                @csrf
                <label for="current_password">Palavra-passe atual</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>

                <label for="password">Nova palavra-passe</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>

                <label for="password_confirmation">Confirmar nova palavra-passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

                <button class="button button-primary button-wide" type="submit">Guardar nova palavra-passe</button>
            </form>

            <!-- Delete Account Section -->
            <div class="settings-divider" aria-hidden="true"></div>

            <form method="POST" action="{{ route('account.destroy') }}" class="form-stack delete-account-section" onsubmit="return confirm('Tem a certeza que quer apagar a sua conta permanentemente? Esta ação não pode ser desfeita.');">
                @csrf
                @method('DELETE')

                <div class="delete-account-warning">
                    <span class="warning-icon" aria-hidden="true">⚠</span>
                    <div>
                        <h3>Esta ação é irreversível</h3>
                        <p>Apagar a tua conta remove permanentemente todos os teus dados, incluindo verificações de personagens e jogos. Não é possível recuperar a conta depois de apagada.</p>
                    </div>
                </div>

                @include('partials.flash')

                <label for="confirm_password">Confirma a tua palavra-passe para apagar a conta</label>
                <input id="confirm_password" name="password" type="password" autocomplete="current-password" required>

                <button class="button button-alert button-wide" type="submit">Apagar a minha conta permanentemente</button>
            </form>
        </div>
    </section>
@endsection