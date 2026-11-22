@extends('layouts.master')

@php($editing = $game->exists)

@section('title', $editing ? 'Confirmar jogo' : 'Novo jogo')

@section('content')
    <a class="back-link" href="{{ $editing ? route('games.show', $game) : route('games.index') }}">← Voltar</a>
    <section class="hero">
        <div>
            <span class="eyebrow">Modo confirmação</span>
            <h1>{{ $editing ? $game->name : 'Adicionar jogo' }}</h1>
            <p>
                @if ($editing)
                    Consulta os dados deste jogo e confirma as personagens que nele aparecem. Só os administradores podem alterar a coleção.
                @else
                    Apenas os administradores podem adicionar jogos. Abre um jogo existente para confirmares as suas personagens.
                @endif
            </p>
        </div>
        <img class="hero-art" src="{{ asset('images/tails-games.png') }}" alt="Tails numa cena de aventura">
    </section>
    @include('partials.flash')
@if ($editing)
        <section class="content-panel debut-panel">
            <div class="panel-heading">
                <div>
                    <h2>Dados do jogo</h2>
                    <p>Confirma que estes dados correspondem a este jogo da franquia Sonic.</p>
                </div>
                <span class="panel-badge panel-badge-blue">Modo confirmação</span>
            </div>
            <div class="debut-feature">
                <div class="debut-art">
                    @if ($game->image)
                        <img src="{{ $game->image }}" alt="Imagem de {{ $game->name }}">
                    @else
                        <span>Imagem não disponível</span>
                    @endif
                </div>
                <div class="debut-copy">
                    <span class="eyebrow">Ficha atual</span>
                    <h3>{{ $game->name }}</h3>
                    <p class="muted">{{ $game->description ?: 'Sem descrição registada.' }}</p>
                    <span class="date-label">Lançado em {{ $game->release_date->format('d/m/Y') }}</span>
                </div>
            </div>
        </section>

        <section class="content-panel">
            <div class="panel-heading">
                <div>
                    <h2>Personagens do jogo</h2>
                    <p>Confirma a presença de cada personagem neste jogo.</p>
                </div>
                <span class="panel-badge">{{ $game->characters->count() }} {{ $game->characters->count() === 1 ? 'personagem' : 'personagens' }}</span>
            </div>
            @forelse ($game->characters as $character)
                <div class="game-name-row">
                    <span>{{ $character->name }}</span>
                    @include('partials.verification-check', [
                        'fact' => 'appearance',
                        'gameId' => $game->id,
                        'label' => 'a presença desta personagem',
                        'isVerified' => $character->appearance_verified_by_user,
                        'verificationCount' => $character->appearance_verifications_count,
                    ])
                </div>
            @empty
                <p class="empty-state">Ainda não há personagens associadas a este jogo.</p>
            @endforelse
        </section>
    @else
        <section class="content-panel">
            <div class="panel-heading">
                <div>
                    <h2>Sem permissões de edição</h2>
                    <p>A tua conta pode confirmar factos, mas não pode criar nem alterar registos.</p>
                </div>
                <span class="panel-badge">Apenas leitura</span>
            </div>
            <p class="empty-state">
                Escolhe um jogo na <a href="{{ route('games.index') }}">coleção</a> para confirmares as suas personagens.
            </p>
        </section>
    @endif
@endsection
