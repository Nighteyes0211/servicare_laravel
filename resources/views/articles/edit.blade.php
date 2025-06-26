
@extends('layouts.app')

@section('title', 'Artikel bearbeiten')

@section('content')
<h1>Artikel bearbeiten</h1>

<!-- Fehlermeldungen anzeigen -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('articles.update', $article->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Artikelnummer -->
    <div class="mb-3">
        <label for="article_number" class="form-label">Artikelnummer</label>
        <input type="text" class="form-control" id="article_number" name="article_number" 
               value="{{ old('article_number', $article->article_number) }}" required>
    </div>

    <!-- Beschreibung -->
    <div class="mb-3">
        <label for="street" class="form-label">Beschreibung</label>
        <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description',$article->description) }}</textarea>
        
    </div>


    <!-- Speichern -->
    <button type="submit" class="btn btn-primary">Speichern</button>
</form>
@endsection
