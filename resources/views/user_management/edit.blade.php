@extends('layouts.app')

@section('title', 'Benutzer bearbeiten')

@section('content')
    <h1>Benutzer bearbeiten</h1>
    <form action="{{ route('user_management.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        
        <!-- Name Field -->
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name', $user->name) }}" 
                class="form-control @error('name') is-invalid @enderror" 
                required
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Field -->
        <div class="mb-3">
            <label for="email" class="form-label">E-Mail</label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="{{ old('email', $user->email) }}" 
                class="form-control @error('email') is-invalid @enderror" 
                required
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
    <input type="checkbox" name="four_day_week" id="four_day_week" value="1" {{ old('four_day_week', $user->four_day_week) ? 'checked' : '' }}>
    <label class="form-check-label" for="four_day_week">4-Tage-Woche (Mo–Do)</label>
</div>

        <!-- Vacation Days Field -->
        <div class="mb-3">
            <label for="vacation_days" class="form-label">Anzahl der Urlaubstage</label>
            <input 
                type="number" 
                name="vacation_days" 
                id="vacation_days" 
                value="{{ old('vacation_days', $user->vacation_days) }}" 
                class="form-control @error('vacation_days') is-invalid @enderror" 
                required
            >
            @error('vacation_days')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Field -->
        <div class="mb-3">
            <label for="password" class="form-label">Passwort (Optional)</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="form-control @error('password') is-invalid @enderror"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Confirmation Field -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Passwort bestätigen</label>
            <input 
                type="password" 
                name="password_confirmation" 
                id="password_confirmation" 
                class="form-control"
            >
        </div>

        <!-- Role Field -->
        <div class="mb-3">
            <label for="role" class="form-label">Rolle</label>
            <select 
                name="role" 
                id="role" 
                class="form-control @error('role') is-invalid @enderror" 
                required
            >
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Mitarbeiter</option>
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Speichern</button>
        <a href="{{ route('user_management.index') }}" class="btn btn-secondary">Abbrechen</a>
    </form>
@endsection
