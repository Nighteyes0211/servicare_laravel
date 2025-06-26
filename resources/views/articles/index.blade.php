@extends('layouts.app')

@section('title', 'Artikel')

@section('content')
    <h1>Artikel</h1>
    <a href="{{ route('articles.create') }}" class="btn btn-primary mb-3">Neuen Artikel anlegen</a>
    <a href="{{ route('articles.import.form') }}" class="btn btn-secondary mb-3">Artikel importieren</a>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Artikelnummer</th>
                <th>Beschreibung</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
                <tr>
                    <td>{{ $article->article_number }}</td>
                    <td>{{ $article->description }}</td>
                    <td>
                        <a href="{{ route('articles.show', $article) }}" class="btn btn-success btn-sm">Anzeigen</a>
                        <a href="{{ route('articles.edit', $article) }}" class="btn btn-primary btn-sm">Bearbeiten</a>
                        <form action="{{ route('articles.destroy', $article) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Löschen</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4 pagination">
        {{ $articles->links() }}
    </div>
@endsection
