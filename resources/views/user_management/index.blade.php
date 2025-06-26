@extends('layouts.app')

@section('title', 'Benutzerverwaltung')

@section('content')
    <h1>Benutzerverwaltung</h1>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('user_management.create') }}" class="btn btn-primary mb-3">Neuen Benutzer hinzufügen</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>E-Mail</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('user_management.show', $user->id) }}" class="btn btn-info btn-sm">Anzeigen</a>
                        <a href="{{ route('user_management.edit', $user->id) }}" class="btn btn-warning btn-sm">Bearbeiten</a>
                        <form action="{{ route('user_management.destroy', $user->id) }}" method="POST" class="d-inline">
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
