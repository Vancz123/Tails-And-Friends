@extends('layouts.master')

@section('title', $character->name)

@section('content')
    <a class="back-link" href="{{ route('characters.index') }}">← Todos os personagens</a>
    <section class="profile-hero">
        <div class="profile-image">
            @if ($character->image)
                <img src="{{ $character->image }}" alt="Imagem de {{ $character->name }}">
            @else
                <img src="{{ asset('images/character-placeholder.png') }}" alt="Imagem por omissão de {{ $character->name }}">
            @endif
        </div>
        <div class="profile-copy">
            <span class="eyebrow">Personagem</span>
            <h1>{{ $character->name }}</h1>
            <p>{{ $character->description ?: 'Um amigo do Sonic.' }}</p>
            <span class="count-pill">{{ $character->games->count() }} {{ $character->games->count() === 1 ? 'jogo' : 'jogos' }} na coleção</span>
            @include('partials.verification-check', [
                'fact' => 'character',
                'label' => 'este personagem',
                'isVerified' => $character->character_verified_by_user,
                'verificationCount' => $character->character_verifications_count,
            ])
        </div>
        @auth
            @if (auth()->user()->isAdministrator())
                <a class="button button-primary" href="{{ route('characters.edit', $character) }}">Editar personagem</a>
            @else
                <a class="button button-outline" href="{{ route('characters.edit', $character) }}">Confirmar personagem</a>
            @endif
        @endauth
    </section>
    @include('partials.flash')

    <section class="content-panel debut-panel">
        <div class="panel-heading">
            <div><h2>Jogo de estreia</h2><p>O jogo em que esta personagem apareceu pela primeira vez na franquia.</p></div>
            <span class="panel-badge">Primeira aparição</span>
        </div>
        @if ($character->debutGame)
            <div class="debut-feature">
                <div class="debut-art">
                    @if ($character->debutGame->image)
                        <img src="{{ $character->debutGame->image }}" alt="Imagem de {{ $character->debutGame->name }}" loading="lazy">
                    @else
                        <span>Imagem não disponível</span>
                    @endif
                </div>
                <div class="debut-copy">
                    <span class="eyebrow">Estreia na franquia</span>
                    <h3>{{ $character->debutGame->name }}</h3>
                    <span class="date-label">{{ $character->debutGame->release_date->format('d/m/Y') }}</span>
                    @include('partials.verification-check', [
                        'fact' => 'debut',
                        'label' => 'este jogo de estreia',
                        'isVerified' => $character->debut_game_verified_by_user,
                        'verificationCount' => $character->debut_game_verifications_count,
                    ])
                </div>
            </div>
        @else
            <p class="empty-state">O jogo de estreia ainda não foi definido. Escolhe-o ao editar a personagem.</p>
        @endif
    </section>

    <section class="content-panel">
        <div class="panel-heading">
            <div><h2>Outros jogos</h2><p>Outros jogos da coleção em que esta personagem aparece.</p></div>
        </div>
        @forelse ($character->games as $game)
            <div class="game-name-row">
                <span>{{ $game->name }}</span>
                @include('partials.verification-check', [
                    'fact' => 'appearance',
                    'gameId' => $game->id,
                    'label' => 'a presença neste jogo',
                    'isVerified' => $game->verified_by_user,
                    'verificationCount' => $game->verification_count,
                ])
            </div>
        @empty
            <p class="empty-state">Ainda não há outros jogos associados a esta personagem.</p>
        @endforelse
    </section>
@endsection
