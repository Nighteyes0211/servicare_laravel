@extends('layouts.app')

@section('title', 'Artikel erstellen')

@section('content')
    <h1>Artikel erstellen</h1>

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

    <!-- Formular für Artikel -->
    <form action="{{ route('articles.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-12 mb-3">
                <label for="article_number" class="form-label">Artikelnummer</label>
                <input type="text" name="article_number" id="article_number" class="form-control" value="{{ old('article_number') }}" required>
            </div>
            <div class="col-md-12">
                <label for="description" class="form-label">Beschreibung</label>
                <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
            </div>
        </div>


        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Artikel speichern</button>
        </div>
    </form>
@endsection
