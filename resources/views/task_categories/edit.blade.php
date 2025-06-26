@extends('layouts.app')

@section('title', 'Kategorie bearbeiten')

@section('content')
<h1>Kategorie bearbeiten</h1>

<form action="{{ route('task_categories.update', $taskCategory->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="name" class="form-label">Name der Kategorie</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $taskCategory->name) }}" required>
    </div>
    <div class="mb-3">
        <label for="icon" class="form-label">Icon hochladen</label>
        <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
    </div>
    @if ($taskCategory->icon)
        <div class="mb-3">
            <p>Aktuelles Icon:</p>
            <img src="{{ asset('storage/' . $taskCategory->icon) }}" alt="Icon" style="width: 100px; height: auto;">
        </div>
    @endif

   

<div class="form-group">
    <label for="color">Farbe (Hex-Code)</label>
    <input type="color" id="color" name="color" class="form-control" value="{{ old('color', $taskCategory->color ?? '#000000') }}">
</div>

    <button type="submit" class="btn btn-primary">Aktualisieren</button>
</form>


@endsection
