@extends('layouts.app')

@section('title', 'Aufgabenkategorien')

@section('content')
<h1>Aufgabenkategorien</h1>
<a href="{{ route('task_categories.create') }}" class="btn btn-primary mb-3">Neue Kategorie erstellen</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Icon</th>
            <th>Name</th>
            <th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>
                    @if ($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}" alt="Icon" style="width: 25px; height: auto;">
                    @else
                        Kein Icon
                    @endif
                </td>
                <td>{{ $category->name }}</td>
                <td>
                    <a href="{{ route('task_categories.edit', $category) }}" class="btn btn-warning btn-sm">Bearbeiten</a>
                    <form action="{{ route('task_categories.destroy', $category) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Löschen</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
