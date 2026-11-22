@extends('layouts.master')
@php($editing = $character->exists)

@section('title', $editing ? 'Confirmar personagem' : 'Novo personagem')

@section('content')
    <a class="back-link" href="{{ $editing ? route('characters.show', $character) : route('characters.index') }}">← Voltar</a>
    <section class="hero">
        <div>
            <span class="eyebrow">Modo confirmação</span>
            <h1>{{ $editing ? $character->name : 'Adicionar personagem' }}</h1>
            <p>
                @if ($editing)
                    Consulta os dados desta ficha e confirma o que sabes. Só os administradores podem alterar a coleção.
                @else
                    Apenas os administradores podem adicionar personagens. Abre uma ficha existente para confirmares os seus
                    dados. O eggman ja tinha destruido a database antes e não podemos deixar isso acontecer outra vez!
                @endif
            </p>
        </div>
        <img class="hero-art" src="{{ asset('images/tails-collection.png') }}" alt="Tails numa moldura dourada">
    </section>
    @include('partials.flash')
    @if ($editing)
        <section class="content-panel debut-panel">
            <div class="panel-heading">
                <div>
                    <h2>Dados do personagem</h2>
                    <p>Confirma que estes dados correspondem à franquia Sonic e se são 1 dos meus amigos haha!</p>
                </div>
                <span class="panel-badge">Modo confirmação</span>
            </div>
            <div class="debut-feature">
                <div class="debut-art">
                    @if ($character->image)
                        <img src="{{ $character->image }}" alt="Imagem de {{ $character->name }}">
                    @else
                        <img src="{{ asset('images/character-placeholder.png') }}"
                            alt="Imagem por omissão de {{ $character->name }}">
                    @endif
                </div>
                <div class="debut-copy">
                    <span class="eyebrow">Ficha atual</span>
                    <h3>{{ $character->name }}</h3>
                    <p class="muted">{{ $character->description ?: 'Sem descrição registada. hum quem é este amigo?' }}</p>
                    @include('partials.verification-check', [
                        'fact' => 'character',
                        'label' => 'este personagem',
                        'isVerified' => $character->character_verified_by_user,
                        'verificationCount' => $character->character_verifications_count,
                    ])
                </div>
            </div>
        </section>
    @else
        <section class="content-panel">
            <div class="panel-heading">
                <div>
                    <h2>Sem permissões de edição</h2>
                    <p>A tua conta pode confirmar factos, mas não pode criar nem alterar registos. isto é para prevenir o
                        eggman de destruir a database outra vez!</p>
                </div>
                <span class="panel-badge">Apenas leitura</span>
            </div>
            <p class="empty-state">
                Escolhe um personagem na <a href="{{ route('characters.index') }}">coleção</a> para confirmares os seus
                dados. Os meus amigos sao todos incriveis!
            </p>
        </section>
    @endif

    @if ($editing)
        <section class="content-panel debut-panel">
            <div class="panel-heading">
                <div>
                    <h2>Jogo de estreia</h2>
                    <p>Confirma o jogo onde esta personagem apareceu pela primeira vez. Realmente novos amigos sao sempre
                        fenomenais!</p>
                </div>
                <span class="panel-badge">Primeira aparição</span>
            </div>
            @if ($character->debutGame)
                <div class="debut-feature">
                    <div class="debut-art">
                        @if ($character->debutGame->image)
                            <img src="{{ $character->debutGame->image }}"
                                alt="Imagem de {{ $character->debutGame->name }}" loading="lazy">
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
                <p class="empty-state">O jogo de estreia ainda não foi definido. Um administrador tem de o escolher
                    primeiro. Temos tantos amigos que as vezes fica dificil nos lembrar-nos onde os conheçemos...</p>
            @endif
        </section>

        <section class="content-panel">
            <div class="panel-heading">
                <div>
                    <h2>Outros jogos</h2>
                    <p>Confirma em que outros jogos esta personagem aparece. Com amigos assim nunca temos que temer o
                        eggman!</p>
                </div>
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
                <p class="empty-state">Ainda não há outros jogos associados a esta personagem. as vezes os nossos amigos tem batalhas em sitios fora da nossa realidade...</p>
            @endforelse
        </section>
    @endif
@endsection
