@extends('layouts.master')

@section('title', 'Personagens')

@section('content')
    <section class="hero">
        <div>
            <span class="eyebrow">A equipa de Sonic</span>
            <h1>Personagens</h1>
            <p>Conhece os amigos do Sonic e descobre em quantos jogos cada um aparece.</p>
        </div>
        <img class="hero-art" src="{{ asset('images/tails-collection.png') }}" alt="Tails em uma moldura dourada" fetchpriority="high">
    </section>
    @include('partials.flash')

    <section class="content-panel">
        <div class="panel-heading">
            <div>
                <h2 class="collection-heading-title">
                    <span>Todos os personagens</span>
                    @auth
                        @if (auth()->user()->isAdministrator())
                            <a class="button button-primary button-small" href="{{ route('characters.create') }}"><span aria-hidden="true">＋</span> Novo personagem</a>
                        @endif
                    @endauth
                </h2>
                <p>{{ $characters->total() }} registos na coleção</p>
            </div>
            <span class="panel-badge">Coleção</span>
        </div>
        <form class="collection-tools" method="GET" action="{{ route('characters.index') }}" role="search">
            <label class="sr-only" for="character-search">Pesquisar personagens</label>
            <input id="character-search" name="q" type="search" value="{{ $search }}" maxlength="100" placeholder="Pesquisar por nome ou descrição...">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">
            <button class="button button-primary button-small" type="submit">Pesquisar</button>
            @if ($search !== '')
                <a class="button button-quiet button-small" href="{{ route('characters.index', ['sort' => $sort, 'direction' => $direction]) }}">Limpar</a>
            @endif
            <div class="collection-sort" role="group" aria-label="Ordenar personagens">
                <span class="collection-sort-label">Ordenar:</span>
                <a class="button {{ $sort === 'name' ? 'button-primary' : 'button-outline' }} button-small" href="{{ route('characters.index', ['q' => $search, 'sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc']) }}" aria-label="{{ $sort === 'name' && $direction === 'desc' ? 'Ordenar alfabeticamente de Z a A' : 'Ordenar alfabeticamente de A a Z' }}" title="{{ $sort === 'name' && $direction === 'desc' ? 'Ordem alfabética Z–A' : 'Ordem alfabética A–Z' }}" @if ($sort === 'name') aria-current="page" @endif>{{ $sort === 'name' && $direction === 'desc' ? 'Z–A' : 'A–Z' }}</a>
                <a class="button {{ $sort === 'debut_release_date' ? 'button-primary' : 'button-outline' }} button-small" href="{{ route('characters.index', ['q' => $search, 'sort' => 'debut_release_date', 'direction' => $sort === 'debut_release_date' && $direction === 'asc' ? 'desc' : 'asc']) }}" aria-label="{{ $sort === 'debut_release_date' && $direction === 'desc' ? 'Ordenar pela data do jogo de estreia, mais recente primeiro' : 'Ordenar pela data do jogo de estreia, mais antigo primeiro' }}" title="{{ $sort === 'debut_release_date' && $direction === 'desc' ? 'Jogo de estreia: mais recente primeiro' : 'Jogo de estreia: mais antigo primeiro' }}" @if ($sort === 'debut_release_date') aria-current="page" @endif>Estreia {{ $sort === 'debut_release_date' && $direction === 'desc' ? '↓' : '↑' }}</a>
                <a class="button {{ $sort === 'created_at' ? 'button-primary' : 'button-outline' }} button-small" href="{{ route('characters.index', ['q' => $search, 'sort' => 'created_at', 'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc']) }}" aria-label="{{ $sort === 'created_at' && $direction === 'desc' ? 'Ordenar pela data de adição, mais recente primeiro' : 'Ordenar pela data de adição, mais antigo primeiro' }}" title="{{ $sort === 'created_at' && $direction === 'desc' ? 'Adicionados: mais recente primeiro' : 'Adicionados: mais antigo primeiro' }}" @if ($sort === 'created_at') aria-current="page" @endif>Adição {{ $sort === 'created_at' && $direction === 'desc' ? '↓' : '↑' }}</a>
            </div>
        </form>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Personagem</th>
                        <th>Jogos</th>
                        <th class="align-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($characters as $character)
                        <tr>
                            <td>
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
                            </td>
                            <td><span class="count-pill">{{ $character->games_count }} {{ $character->games_count === 1 ? 'jogo' : 'jogos' }}</span></td>
                            <td class="align-right">
                                <div class="action-row">
                                    <a class="button button-outline button-small" href="{{ route('characters.show', $character) }}">Ver jogos <span>→</span></a>
                                    @auth
                                        <a class="button button-primary button-small" href="{{ route('characters.edit', $character) }}" aria-label="Editar {{ $character->name }}" title="Editar personagem">Editar</a>
                                        @if (auth()->user()->isAdministrator())
                                            <form method="POST" action="{{ route('characters.destroy', $character) }}" onsubmit="return confirm('Remover este personagem?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button button-primary button-small" type="submit" aria-label="Remover {{ $character->name }}" title="Remover personagem">Apagar</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="3">{{ $search !== '' ? 'Nenhum personagem corresponde à pesquisa.' : 'Ainda não existem personagens. Regista o primeiro para começar.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $characters->links() }}</div>
    </section>
@endsection
