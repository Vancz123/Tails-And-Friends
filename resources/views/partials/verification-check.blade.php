<div class="fact-verification">
    <span class="fact-verification-count">
        {{ $verificationCount }} {{ $verificationCount === 1 ? 'confirmação' : 'confirmações' }}
    </span>
    @auth
        <form method="POST" action="{{ route('characters.verifications.update', $character) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="fact" value="{{ $fact }}">
            @isset($gameId)
                <input type="hidden" name="game_id" value="{{ $gameId }}">
            @endisset
            <input type="hidden" name="checked" value="0">
            <label class="verification-checkbox">
                <input type="checkbox" name="checked" value="1" @checked($isVerified)>
                <span>{{ $isVerified ? 'Confirmado por ti: '.$label : 'Confirmar '.$label }}</span>
            </label>
            <button class="button button-outline button-small" type="submit">Guardar check</button>
        </form>
    @else
        <a class="button button-outline button-small" href="{{ route('login') }}">Entra para confirmar</a>
    @endauth
</div>
