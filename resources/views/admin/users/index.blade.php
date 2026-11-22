@extends('layouts.master')

@section('title', 'Gestão de utilizadores')

@section('content')
    <section class="hero">
        <div>
            <span class="eyebrow">Painel de administração</span>
            <h1>Utilizadores</h1>
            <p>Consulta as contas registadas, gere permissões e remove contas que já não pertencem à equipa.</p>
        </div>
        <img class="hero-art" src="{{ asset('images/tails-users.png') }}" alt="Tails a organizar a equipa de amigos">
    </section>
    @include('partials.flash')

    <section class="content-panel">
        <div class="panel-heading">
            <div>
                <h2>Contas registadas</h2>
                <p>{{ $users->total() }} {{ $users->total() === 1 ? 'utilizador' : 'utilizadores' }}</p>
            </div>
            <span class="panel-badge">Administração</span>
        </div>
        <form class="collection-tools" method="GET" action="{{ route('admin.users.index') }}" role="search">
            <label class="sr-only" for="user-search">Pesquisar utilizadores</label>
            <input id="user-search" name="q" type="search" value="{{ $search }}" maxlength="100" placeholder="Pesquisar por nome ou e-mail...">
            <button class="button button-primary button-small" type="submit">Pesquisar</button>
            @if ($search !== '')
                <a class="button button-quiet button-small" href="{{ route('admin.users.index') }}">Limpar</a>
            @endif
        </form>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr><th>Utilizador</th><th>Permissão</th><th class="align-right">Ações</th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="user-identity">
                                    <strong>{{ $user->name }}</strong>
                                    <small>{{ $user->email }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge {{ $user->isAdministrator() ? 'role-badge-admin' : '' }}">
                                    {{ $user->isAdministrator() ? 'Administrador' : 'Utilizador' }}
                                </span>
                            </td>
                            <td class="align-right">
                                @if ($user->id === auth()->id())
                                    <span class="muted">A tua conta</span>
                                @else
<<<<<<< HEAD
                                    <div class="user-actions">
                                        <form class="role-form" method="POST" action="{{ route('admin.users.update-role', $user) }}">
                                            @csrf
                                            @method('PUT')
                                            <label class="sr-only" for="role-{{ $user->id }}">Permissão de {{ $user->name }}</label>
                                            <select id="role-{{ $user->id }}" name="role">
                                                <option value="user" @selected($user->role === 'user')>Utilizador</option>
                                                <option value="admin" @selected($user->role === 'admin')>Administrador</option>
                                            </select>
                                            <button class="button button-outline button-small" type="submit">Guardar</button>
                                        </form>
                                        <form class="user-delete-form" method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Apagar a conta de {{ $user->name }}? Esta ação não pode ser desfeita.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button button-danger button-small" type="submit"
                                                aria-label="Apagar a conta de {{ $user->name }}">Apagar</button>
                                        </form>
                                    </div>
=======
                                    <form class="role-form" method="POST" action="{{ route('admin.users.update-role', $user) }}">
                                        @csrf
                                        @method('PUT')
                                        <label class="sr-only" for="role-{{ $user->id }}">Permissão de {{ $user->name }}</label>
                                        <select id="role-{{ $user->id }}" name="role">
                                            <option value="user" @selected($user->role === 'user')>Utilizador</option>
                                            <option value="admin" @selected($user->role === 'admin')>Administrador</option>
                                        </select>
                                        <button class="button button-outline button-small" type="submit">Guardar</button>
                                    </form>
                                    <form class="role-form" method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Tem a certeza que queres apagar este utilizador? Esta ação não pode ser desfeita.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button button-danger button-small" type="submit">Apagar</button>
                                    </form>
>>>>>>> 51ef348453b200e99430a06005b28634517d253b
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="3">{{ $search !== '' ? 'Nenhum utilizador corresponde à pesquisa.' : 'Ainda não existem utilizadores.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $users->links() }}</div>
    </section>
@endsection
