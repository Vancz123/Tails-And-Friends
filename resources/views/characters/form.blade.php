@extends('layouts.master')

@php($editing = $character->exists)

@section('title', $editing ? 'Editar personagem' : 'Novo personagem')

@section('content')
    <a class="back-link" href="{{ $editing ? route('characters.show', $character) : route('characters.index') }}">← Voltar</a>
    <section class="form-page">
        <div class="form-intro">
            <span class="eyebrow">{{ $editing ? 'Atualizar coleção' : 'Expandir coleção' }}</span>
            <h1>{{ $editing ? 'Editar personagem' : 'Novo personagem' }}</h1>
            <p>Adiciona os detalhes e a imagem que identificam este amigo do Sonic.</p>
        </div>
        @include('partials.flash')
        <form class="content-panel form-card form-stack" method="POST" enctype="multipart/form-data" action="{{ $editing ? route('characters.update', $character) : route('characters.store') }}">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif
            <div class="field-group">
                <label for="name">Nome do personagem <span class="required-mark">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $character->name) }}" maxlength="255" required autofocus>
            </div>
            <div class="field-group">
                <label for="image">Imagem do personagem</label>
                @if ($character->image)
                    <div class="image-preview" data-image-preview>
                        <img src="{{ $character->image }}" alt="Imagem atual de {{ $character->name }}" data-image-preview-image>
                        <span data-image-preview-label>Imagem atual</span>
                    </div>
                @else
                    <div class="image-preview" data-image-preview>
                        <img src="{{ asset('images/character-placeholder.png') }}" alt="Imagem por omissão de personagem" data-image-preview-image>
                        <span data-image-preview-label>Imagem por omissão</span>
                    </div>
                @endif
                <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-image-preview-input>
                <small>JPG, PNG ou WEBP; máximo 2 MB. Deixa em branco para manter a imagem atual.</small>
            </div>
            <div class="field-group">
                <label for="debut_game_id">Jogo de estreia na franquia</label>
                <select id="debut_game_id" name="debut_game_id">
                    <option value="">Por definir</option>
                    @foreach ($games as $game)
                        <option value="{{ $game->id }}" @selected((string) old('debut_game_id', $character->debut_game_id) === (string) $game->id)>
                            {{ $game->name }}
                        </option>
                    @endforeach
                </select>
                @if ($games->isEmpty())
                    <small>Não há jogos na coleção. <a href="{{ route('games.create') }}">Adiciona um jogo</a> para poderes escolhê-lo como estreia.</small>
                @else
                    <small>Ao escolher um jogo, ele é associado automaticamente ao personagem.</small>
                @endif
            </div>
            <div class="field-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="4" maxlength="2000" placeholder="Uma pequena descrição do personagem...">{{ old('description', $character->description) }}</textarea>
            </div>
            <div class="form-actions">
                <a class="button button-quiet" href="{{ $editing ? route('characters.show', $character) : route('characters.index') }}">Cancelar</a>
                <button class="button button-primary" type="submit">{{ $editing ? 'Guardar alterações' : 'Adicionar personagem' }}</button>
            </div>
        </form>
    </section>
@endsection
