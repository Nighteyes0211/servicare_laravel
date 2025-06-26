@extends('layouts.app')

@section('title', 'Neue Kategorie erstellen')

@section('content')
<h1>Neue Kategorie erstellen</h1>

<form action="{{ route('task_categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name der Kategorie</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="icon" class="form-label">Icon hochladen</label>
        <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
    </div>
     <div class="form-group">
    <label for="color">Farbe (Hex-Code)</label>
    <input type="color" id="color" name="color" class="form-control" value="{{ old('color', $taskCategory->color ?? '#000000') }}">
</div>
    <button type="submit" class="btn btn-success">Speichern</button>
</form>
@endsection
