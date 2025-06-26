@extends('layouts.app')

@section('title', 'Artikel importieren')

@section('content')
    <h1>Artikel importieren</h1>

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
    <form action="{{ route('articles.import') }}" method="POST"  enctype="multipart/form-data">
        @csrf

        <div class="row mb-3">
            <div class="col-md-12 mb-3">
                <label for="file" class="form-label"> Wählen Sie Datei</label>
                <input type="file" name="file" id="file" class="form-control" value="{{ old('file') }}" required>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Artikel speichern</button>
        </div>
    </form>
@endsection
