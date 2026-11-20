@extends('layouts.master')

@php($editing = $game->exists)

@section('title', $editing ? 'Editar jogo' : 'Novo jogo')

@section('content')
    <a class="back-link" href="{{ route('games.index') }}">← Voltar aos jogos</a>
    <section class="form-page">
        <div class="form-intro">
            <span class="eyebrow">{{ $editing ? 'Atualizar coleção' : 'Expandir coleção' }}</span>
            <h1>{{ $editing ? 'Editar jogo' : 'Novo jogo' }}</h1>
            <p>Regista a imagem, a data de lançamento e as personagens que aparecem no jogo.</p>
        </div>
        @include('partials.flash')
        <form class="content-panel form-card form-stack" method="POST" enctype="multipart/form-data" action="{{ $editing ? route('games.update', $game) : route('games.store') }}">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif
            <div class="field-group">
                <label for="name">Nome do jogo <span class="required-mark">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $game->name) }}" maxlength="255" required autofocus>
            </div>
            <div class="field-grid">
                <div class="field-group">
                    <label for="release_date">Data de lançamento <span class="required-mark">*</span></label>
                    <input id="release_date" name="release_date" type="date" value="{{ old('release_date', $game->release_date?->format('Y-m-d')) }}" required>
                </div>
                <div class="field-group">
                    <label for="image">Imagem do jogo</label>
                    @if ($game->image)
                        <div class="image-preview" data-image-preview>
                            <img src="{{ $game->image }}" alt="Imagem atual de {{ $game->name }}" data-image-preview-image>
                            <span data-image-preview-label>Imagem atual</span>
                        </div>
                    @else
                        <div class="image-preview" data-image-preview hidden>
                            <img alt="" data-image-preview-image>
                            <span data-image-preview-label></span>
                        </div>
                    @endif
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-image-preview-input>
                    <small>JPG, PNG ou WEBP; máximo 2 MB. Deixa em branco para manter a imagem atual.</small>
                </div>
            </div>
            <div class="field-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="3" maxlength="2000" placeholder="Uma pequena descrição do jogo...">{{ old('description', $game->description) }}</textarea>
            </div>
            <fieldset class="field-group character-picker">
                <legend>Personagens que aparecem</legend>
                <p>Seleciona os personagens associados a este jogo.</p>
                @php($selectedIds = old('character_ids', $game->characters->modelKeys()))
                <div class="checkbox-grid">
                    @forelse ($characters as $character)
                        <label class="check-card" for="character-{{ $character->id }}">
                            <input id="character-{{ $character->id }}" name="character_ids[]" type="checkbox" value="{{ $character->id }}" @checked(in_array($character->id, $selectedIds))>
                            <span>{{ $character->name }}</span>
                        </label>
                    @empty
                        <span class="muted">Adiciona personagens antes de os associares a um jogo.</span>
                    @endforelse
                </div>
            </fieldset>
            <div class="form-actions">
                <a class="button button-quiet" href="{{ route('games.index') }}">Cancelar</a>
                <button class="button button-primary" type="submit">{{ $editing ? 'Guardar alterações' : 'Adicionar jogo' }}</button>
            </div>
        </form>
    </section>
@endsection
