@extends('layouts.app')

@section('title', 'Artikeldetaljer')

@section('content')
    <h1>Artikeldetaljer</h1>

    <!-- Artikeldetaljer -->
    <div class="card shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-primary">{{ $article->article_number }}</h1>
            <a href="{{ route('articles.edit', $article) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Bearbeiten
            </a>
        </div>

        <div class="mb-3">
            <p><strong>Artikelnummer:</strong> <span class="text-secondary">{{ $article->article_number }}</span></p>
            <p><strong>Beschreibung:</strong> <span class="text-secondary">{{ $article->description }}</span></p>
        </div>

        <div class="text-end">
            <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> Zurück zur Liste
            </a>
        </div>
    </div>


@endsection
