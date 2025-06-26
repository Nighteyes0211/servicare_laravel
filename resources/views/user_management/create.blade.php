@extends('layouts.app')

@section('title', 'Neuen Benutzer hinzufügen')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <h1>Neuen Benutzer hinzufügen</h1>
    <form action="{{ route('user_management.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-Mail</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="four_day_week" id="four_day_week" value="1"{{ old('four_day_week') ? 'checked' : '' }}>
            <label class="form-check-label" for="four_day_week">4-Tage-Woche (Mo–Do)</label>
        </div>

        <div class="mb-3">
            <label for="vacation_days" class="form-label">Verfügbare Urlaubstage</label>
            <input type="vacation_days" name="vacation_days" id="vacation_days" value="{{ old('vacation_days') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Passwort</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Passwort bestätigen</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Rolle</label>
            <select name="role" id="role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="employee">Mitarbeiter</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Benutzer hinzufügen</button>
        <a href="{{ route('user_management.index') }}" class="btn btn-secondary">Abbrechen</a>
    </form>
@endsection
