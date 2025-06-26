
@extends('layouts.app')

@section('title', 'Kunden bearbeiten')

@section('content')
<h1>Kunde bearbeiten</h1>

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

<form action="{{ route('customers.update', $customer->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Firmenname -->
    <div class="mb-3">
        <label for="company_name" class="form-label">Firmenname</label>
        <input type="text" class="form-control" id="company_name" name="company_name" 
               value="{{ old('company_name', $customer->company_name) }}" required>
    </div>

    <!-- Straße -->
    <div class="mb-3">
        <label for="street" class="form-label">Straße & Hausnummer</label>
        <input type="text" class="form-control" id="street" name="street" 
               value="{{ old('street', $customer->street) }}" required>
    </div>


    <!-- Postleitzahl -->
    <div class="mb-3">
        <label for="postal_code" class="form-label">Postleitzahl</label>
        <input type="text" class="form-control" id="postal_code" name="postal_code" 
               value="{{ old('postal_code', $customer->postal_code) }}" required>
    </div>

    <!-- Stadt -->
    <div class="mb-3">
        <label for="city" class="form-label">Stadt</label>
        <input type="text" class="form-control" id="city" name="city" 
               value="{{ old('city', $customer->city) }}" required>
    </div>

    <!-- Land -->
    <div class="mb-3">
        <label for="country" class="form-label">Land</label>
        <input type="text" class="form-control" id="country" name="country" 
               value="{{ old('country', $customer->country) }}">
    </div>

    <!-- Telefonnummer -->
    <div class="mb-3">
        <label for="phone" class="form-label">Telefonnummer</label>
        <input type="text" class="form-control" id="phone" name="phone" 
               value="{{ old('phone', $customer->phone) }}">
    </div>

    <!-- E-Mail-Adresse -->
    <div class="mb-3">
        <label for="email" class="form-label">E-Mail-Adresse</label>
        <input type="email" class="form-control" id="email" name="email" 
               value="{{ old('email', $customer->email) }}">
    </div>

    <!-- Speichern -->
    <button type="submit" class="btn btn-primary">Speichern</button>
</form>

<hr>

<div class="card shadow-sm p-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5">Ansprechpartner</h2>
        <a href="{{ route('contacts.create', $customer) }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Ansprechpartner hinzufügen
        </a>
    </div>

    @if ($customer->contacts->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Anrede</th>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>Telefon</th>
                        <th>Mobil</th>
                        <th>E-Mail</th>
                        <th>Position</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer->contacts as $contact)
                        <tr>
                            <td>{{ $contact->salutation }}</td>
                            <td>{{ $contact->first_name }}</td>
                            <td>{{ $contact->last_name }}</td>
                            <td>{{ $contact->phone ?? '—' }}</td>
                            <td>{{ $contact->mobile ?? '—' }}</td>
                            <td>{{ $contact->email ?? '—' }}</td>
                            <td>{{ $contact->position ?? '—' }}</td>
                            <td>
                                <form action="{{ route('contacts.destroy', [$customer, $contact]) }}" method="POST" onsubmit="return confirm('Wirklich löschen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Löschen
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted">Keine Ansprechpartner vorhanden.</p>
    @endif
</div>




@endsection
