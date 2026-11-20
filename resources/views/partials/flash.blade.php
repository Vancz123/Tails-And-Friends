@if (session('status'))
    <div class="notice notice-success" role="status">
        {{ session('status') === \Laravel\Fortify\Fortify::PASSWORD_UPDATED ? 'Palavra-passe atualizada com sucesso.' : session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="notice notice-error" role="alert">
        <strong>Não foi possível guardar.</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
