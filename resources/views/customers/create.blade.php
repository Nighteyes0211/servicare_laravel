@extends('layouts.app')

@section('title', 'Kunden erstellen')

@section('content')
    <h1>Kunden erstellen</h1>

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

    <!-- Formular für Kunden -->
    <form action="{{ route('customers.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="company_name" class="form-label">Firmenname</label>
                <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name') }}" required>
            </div>
            <div class="col-md-6">
                <label for="street" class="form-label">Straße</label>
                <input type="text" name="street" id="street" class="form-control" value="{{ old('street') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="house_number" class="form-label">Hausnummer</label>
                <input type="text" name="house_number" id="house_number" class="form-control" value="{{ old('house_number') }}" required>
            </div>
            <div class="col-md-6">
                <label for="postal_code" class="form-label">Postleitzahl</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="city" class="form-label">Stadt</label>
                <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}" required>
            </div>
            <div class="col-md-6">
                <label for="country" class="form-label">Land</label>
                <input type="text" name="country" id="country" class="form-control" value="{{ old('country') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="phone" class="form-label">Telefonnummer</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">E-Mail-Adresse</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Kunde speichern</button>
        </div>
    </form>
@endsection
