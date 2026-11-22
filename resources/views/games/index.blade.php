@extends('layouts.master')

@section('title', 'Jogos')

@section('content')
    <section class="hero">
        <div>
            <span class="eyebrow">A aventura continua</span>
            <h1>Jogos</h1>
            <p>Consulta os jogos da coleção, as datas de lançamento e as personagens que participam.</p>
        </div>
        <img class="hero-art" src="{{ asset('images/tails-games.png') }}" alt="Tails em uma cena de aventura"
            fetchpriority="high">
    </section>
    @include('partials.flash')

    <section class="content-panel">
        <div class="panel-heading">
            <div>
                <h2 class="collection-heading-title">
                    <span>Todos os jogos</span>
                    @auth
                        @if (auth()->user()->isAdministrator())
                            <a class="button button-primary button-small" href="{{ route('games.create') }}"><span
                                    aria-hidden="true">＋</span> Novo jogo</a>
                        @endif
                    @endauth
                </h2>
                <p>{{ $games->total() }} registos na coleção</p>
            </div>
            <span class="panel-badge panel-badge-blue">Lançamentos</span>
        </div>
        <form class="collection-tools" method="GET" action="{{ route('games.index') }}" role="search">
            <label class="sr-only" for="game-search">Pesquisar jogos ou personagens. Encontra um dos meus amigos!</label>
            <input id="game-search" name="q" type="search" value="{{ $search }}" maxlength="100"
                placeholder="Pesquisar por jogo, personagem ou descrição...">
            <label class="sr-only" for="game-year">Filtrar por ano</label>
            <input id="game-year" class="year-filter" name="year" type="number" min="1991" max="2100"
                value="{{ $year }}" placeholder="Ano">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">
            <button class="button button-primary button-small" type="submit">Filtrar</button>
            @if ($search !== '' || $year !== '')
                <a class="button button-quiet button-small"
                    href="{{ route('games.index', ['sort' => $sort, 'direction' => $direction]) }}">Limpar</a>
            @endif
            <div class="collection-sort" role="group" aria-label="Ordenar jogos">
                <span class="collection-sort-label">Ordenar:</span>
                <a class="button {{ $sort === 'name' ? 'button-primary' : 'button-outline' }} button-small"
                    href="{{ route('games.index', ['q' => $search, 'year' => $year, 'sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                    aria-label="{{ $sort === 'name' && $direction === 'desc' ? 'Ordenar alfabeticamente de Z a A' : 'Ordenar alfabeticamente de A a Z' }}"
                    title="{{ $sort === 'name' && $direction === 'desc' ? 'Ordem alfabética Z–A' : 'Ordem alfabética A–Z' }}"
                    @if ($sort === 'name') aria-current="page" @endif>{{ $sort === 'name' && $direction === 'desc' ? 'Z–A' : 'A–Z' }}</a>
                <a class="button {{ $sort === 'release_date' ? 'button-primary' : 'button-outline' }} button-small"
                    href="{{ route('games.index', ['q' => $search, 'year' => $year, 'sort' => 'release_date', 'direction' => $sort === 'release_date' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                    aria-label="{{ $sort === 'release_date' && $direction === 'desc' ? 'Ordenar pela data de lançamento, mais recente primeiro' : 'Ordenar pela data de lançamento, mais antigo primeiro' }}"
                    title="{{ $sort === 'release_date' && $direction === 'desc' ? 'Lançamento: mais recente primeiro' : 'Lançamento: mais antigo primeiro' }}"
                    @if ($sort === 'release_date') aria-current="page" @endif>Lançamento
                    {{ $sort === 'release_date' && $direction === 'desc' ? '↓' : '↑' }}</a>
                <a class="button {{ $sort === 'created_at' ? 'button-primary' : 'button-outline' }} button-small"
                    href="{{ route('games.index', ['q' => $search, 'year' => $year, 'sort' => 'created_at', 'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                    aria-label="{{ $sort === 'created_at' && $direction === 'desc' ? 'Ordenar pela data de adição, mais recente primeiro' : 'Ordenar pela data de adição, mais antigo primeiro' }}"
                    title="{{ $sort === 'created_at' && $direction === 'desc' ? 'Adicionados: mais recente primeiro' : 'Adicionados: mais antigo primeiro' }}"
                    @if ($sort === 'created_at') aria-current="page" @endif>Adição
                    {{ $sort === 'created_at' && $direction === 'desc' ? '↓' : '↑' }}</a>
            </div>
        </form>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Jogo</th>
                        <th>Lançamento</th>
                        <th>Personagens</th>
                        <th>Estreia</th>
                        <th>Aparecem</th>
                        <th class="align-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($games as $game)
                        <tr>
                            <td>
                                <div class="identity-cell">
                                    <div class="game-thumb">
                                        @if ($game->image)
                                            <img src="{{ $game->image }}" alt="Capa de {{ $game->name }}"
                                                loading="lazy">
                                        @else
                                            <span>▶</span>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $game->name }}</strong><small>{{ $game->description ?: 'Jogo da coleção Sonic' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="date-label">{{ $game->release_date->format('d/m/Y') }}</span></td>
                            <td><span class="count-pill">{{ $game->characters_count }}
                                    {{ $game->characters_count === 1 ? 'personagem' : 'personagens' }}</span></td>
                            <td><span class="count-pill">{{ $game->debut_characters_count }}
                                    {{ $game->debut_characters_count === 1 ? 'personagem' : 'personagens' }}</span></td>
                            <td><span class="count-pill">{{ $game->appearing_characters_count }}
                                    {{ $game->appearing_characters_count === 1 ? 'personagem' : 'personagens' }}</span>
                            </td>
                            <td class="align-right">
                                <div class="action-row">
                                    <a class="button button-outline button-small"
                                        href="{{ route('games.show', $game) }}">Ver jogo</a>
                                    @auth
                                        @if (auth()->user()->isAdministrator())
                                            <a class="button button-primary button-small"
                                                href="{{ route('games.edit', $game) }}"
                                                aria-label="Editar {{ $game->name }}">Editar</a>
                                        @else
                                            <a class="button button-outline button-small"
                                                href="{{ route('games.edit', $game) }}"
                                                aria-label="Confirmar {{ $game->name }}"
                                                title="Confirmar personagens do jogo">Confirmar</a>
                                        @endif
                                        @if (auth()->user()->isAdministrator())
                                            <form method="POST" action="{{ route('games.destroy', $game) }}"
                                                onsubmit="return confirm('Remover este jogo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button button-primary button-small" type="submit"
                                                    aria-label="Remover {{ $game->name }}">Apagar</button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="muted">Consulta pública</span>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty-state" colspan="6">
                                {{ $search !== '' || $year !== '' ? 'Nenhum jogo corresponde aos filtros.' : 'Ainda não existem jogos na coleção.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $games->links() }}</div>
    </section>
@endsection
