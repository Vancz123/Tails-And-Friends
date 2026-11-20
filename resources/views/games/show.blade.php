@extends('layouts.master')

@section('title', $game->name)

@section('content')
    <a class="back-link" href="{{ route('games.index') }}">← Todos os jogos</a>
    <section class="profile-hero">
        <div class="profile-image">
            @if ($game->image)
                <img src="{{ $game->image }}" alt="Imagem de {{ $game->name }}">
            @else
                <span aria-hidden="true">▶</span>
            @endif
        </div>
        <div class="profile-copy">
            <span class="eyebrow">Jogo</span>
            <h1>{{ $game->name }}</h1>
            <p>{{ $game->description ?: 'Uma aventura do universo Sonic.' }}</p>
            <span class="date-label">Lançado em {{ $game->release_date->format('d/m/Y') }}</span>
        </div>
        @auth
            <a class="button button-outline" href="{{ route('games.edit', $game) }}">Editar jogo</a>
        @endauth
    </section>
    @include('partials.flash')

    <section class="content-panel game-character-panel" data-game-character-tabs>
        <div class="panel-heading">
            <div>
                <h2>Personagens do jogo</h2>
                <p>Consulta as personagens presentes e as que fizeram a sua estreia nesta aventura.</p>
            </div>
        </div>
        <div class="game-character-tablist" role="tablist" aria-label="Personagens do jogo">
            <button
                class="game-character-tab"
                id="present-characters-tab"
                type="button"
                role="tab"
                aria-selected="true"
                aria-controls="present-characters-panel"
                tabindex="0"
                data-game-character-tab
            >
                Personagens presentes
                <span class="game-character-tab-count">{{ $appearingCharacters->count() }}</span>
            </button>
            <button
                class="game-character-tab"
                id="debut-characters-tab"
                type="button"
                role="tab"
                aria-selected="false"
                aria-controls="debut-characters-panel"
                tabindex="-1"
                data-game-character-tab
            >
                Personagens novos de estreia
                <span class="game-character-tab-count">{{ $debutCharacters->count() }}</span>
            </button>
        </div>
        <div
            class="game-character-tab-panel"
            id="present-characters-panel"
            role="tabpanel"
            aria-labelledby="present-characters-tab"
            tabindex="0"
            data-game-character-panel
        >
            @forelse ($appearingCharacters as $character)
                <a class="game-name-row" href="{{ route('characters.show', $character) }}">
                    <div class="identity-cell">
                        <div class="avatar">
                            @if ($character->image)
                                <img src="{{ $character->image }}" alt="Imagem de {{ $character->name }}" loading="lazy">
                            @else
                                <img src="{{ asset('images/character-placeholder.png') }}" alt="Imagem por omissão de {{ $character->name }}" loading="lazy">
                            @endif
                        </div>
                        <div><strong>{{ $character->name }}</strong><small>{{ $character->description ?: 'Amigo do Sonic' }}</small></div>
                    </div>
                    <span class="count-pill">Ver personagem →</span>
                </a>
            @empty
                <p class="empty-state">Ainda não há outras personagens associadas a este jogo.</p>
            @endforelse
        </div>
        <div
            class="game-character-tab-panel"
            id="debut-characters-panel"
            role="tabpanel"
            aria-labelledby="debut-characters-tab"
            tabindex="0"
            data-game-character-panel
            hidden
        >
            @forelse ($debutCharacters as $character)
                <a class="game-name-row" href="{{ route('characters.show', $character) }}">
                    <div class="identity-cell">
                        <div class="avatar">
                            @if ($character->image)
                                <img src="{{ $character->image }}" alt="Imagem de {{ $character->name }}" loading="lazy">
                            @else
                                <img src="{{ asset('images/character-placeholder.png') }}" alt="Imagem por omissão de {{ $character->name }}" loading="lazy">
                            @endif
                        </div>
                        <div><strong>{{ $character->name }}</strong><small>{{ $character->description ?: 'Amigo do Sonic' }}</small></div>
                    </div>
                    <span class="count-pill">Ver personagem →</span>
                </a>
            @empty
                <p class="empty-state">Ainda não há personagens com estreia definida neste jogo.</p>
            @endforelse
        </div>
    </section>
@endsection
