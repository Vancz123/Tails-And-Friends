@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="hero hero-dashboard">
        <div class="dashboard-hero-copy">
            <span class="eyebrow">Área pessoal</span>
            <h1>Olá, {{ auth()->user()->name }}</h1>
            <p>Bem-vindo ao teu espaço Tails & Friends. Continua a explorar ou ajuda a completar a coleção.</p>
            @if (auth()->user()->isAdministrator())
                <form class="dashboard-simulation-form" method="POST" action="{{ route('security.simulate') }}">
                    @csrf
                    <button class="button button-alert button-pulse" type="submit">
                        <span aria-hidden="true">⚠</span>
                        Simular Invasão do Dr. Eggman
                    </button>
                    <small class="simulation-caption">Demonstração segura · eventos sintéticos e rate limit</small>
                </form>
            @endif
        </div>
        <img class="dashboard-tails" src="{{ asset('images/tails-dashboard.png') }}" alt="Tails a fazer sinal positivo">
    </section>
    <div class="dashboard-links">
        <a class="dashboard-tile" href="{{ route('characters.index') }}">
            <span class="tile-icon">01</span>
            <span><strong>Personagens</strong><small>Explorar a coleção e os jogos de cada um</small></span>
            <span class="tile-arrow">→</span>
        </a>
        <a class="dashboard-tile" href="{{ route('games.index') }}">
            <span class="tile-icon tile-icon-blue">02</span>
            <span><strong>Jogos</strong><small>Consultar datas de lançamento e personagens</small></span>
            <span class="tile-arrow">→</span>
        </a>
        @if (auth()->user()->isAdministrator())
            <a class="dashboard-tile" href="{{ route('security.index') }}">
                <span class="tile-icon tile-icon-blue">03</span>
                <span><strong>Segurança</strong><small>Consultar incidentes e executar a simulação defensiva</small></span>
                <span class="tile-arrow">→</span>
            </a>
        @endif
    </div>
@endsection
