@extends('layouts.app')

@section('title', 'Diagnose und Aufnahme')
@section('content')


<div class="form-container">


    <h1>Diagnosebogen bearbeiten</h1>
    <form action="{{ route('diagnoses.update', $diagnosis) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Customer Dropdown -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label" for="customer_id">Kunde/Einrichtung:</label>
                <select name="customer_id" id="customer_id" class="form-select">
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ $diagnosis->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->company_name }}
                    </option>
                    @endforeach
                </select>

                <!-- Name -->
                <label class="form-label" for="name">Name:</label>
                <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $diagnosis->name) }}" required  ">

                <!-- Phone -->
                <label class="form-label" for="phone">Telefonnummer:</label>
                <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', $diagnosis->phone) }}" required  ">

                <!-- Address -->
                <label class="form-label" for="address">Adresse:</label>
                <textarea name="address" id="address" required class="form-control">{{ old('street', $customer->street) }}</textarea>
            </div>

            <div class="col-md-6">
                <!-- Diagnosedetails -->
                <label class="form-label" for="diagnosis_details">Diagnosedetails:</label>
                <textarea name="diagnosis_details" id="diagnosis_details" class="form-control" required >{{ old('diagnosis_details', $diagnosis->diagnosis_details) }}</textarea>

                <!-- Diagnosedatum -->
                <label class="form-label" for="diagnosis_date">Diagnosedatum:</label>
                <input class="form-control" type="date" name="diagnosis_date" id="diagnosis_date" value="{{ old('diagnosis_date', $diagnosis->diagnosis_date) }}" required  ">

                <!-- Type Section -->
                <div class="checkbox-group">
                    <label class="form-label">
                        <input class="form-check-input" type="checkbox" name="type[]" value="repair" {{ !$diagnosis->repair ?: 'checked' }}> Reparatur
                    </label>
                    <label class="form-label">
                        <input class="form-check-input" type="checkbox" name="type[]" value="complaint" {{ !$diagnosis->complaint ?: 'checked' }}> Reklamation
                    </label>
                    <label class="form-label">
                        <input class="form-check-input" type="checkbox" name="type[]" value="confirmation" {{ !$diagnosis->confirmation ?: 'checked' }}> Auftragsbestätigung
                    </label>
                    <label class="form-label">
                        <input class="form-check-input" type="checkbox" name="type[]" value="inquiry" {{ !$diagnosis->inquiry ?: 'checked' }}> Anfrage
                    </label>
                    <label class="form-label">
                        <input class="form-check-input" type="checkbox" name="type[]" value="quote" {{ !$diagnosis->quote ?: 'checked' }}> KV erstellen
                    </label>
                    <label class="form-label">
                        <input class="form-check-input" type="checkbox" name="type[]" value="maintenance" {{ !$diagnosis->maintenance ?: 'checked' }}> Wartung
                    </label>
                    <label class="form-label">
                        <input class="form-check-input" type="checkbox" name="type[]" value="note" {{ !$diagnosis->note ?: 'checked' }}> Gesprächsnotiz
                    </label>
                    <label class="form-label">
                        <input class="form-check-input" type="checkbox" name="type[]" value="order" {{ !$diagnosis->order ?: 'checked' }}> Bestellung
                    </label>
                </div>
            </div>
        </div>
            <!-- Action Section -->
            <div class="checkbox-section">
                Aufgenommen durch: 
                <label class="form-label">
                    <input type="checkbox" name="action[]" value="PB" {{ !$diagnosis->pb ?: 'checked' }}> PB
                </label>
                <label class="form-label">
                    <input type="checkbox" name="action[]" value="PW" {{ !$diagnosis->pw ?: 'checked' }}> PW
                </label>
                <label class="form-label">
                    <input type="checkbox" name="action[]" value="JT" {{ !$diagnosis->jt ?: 'checked' }}> JT
                </label>
                <label class="form-label">
                    <input type="checkbox" name="action[]" value="DM" {{ !$diagnosis->dm ?: 'checked' }}> DM
                </label>
                <label class="form-label">
                    <input type="checkbox" name="action[]" value="other" {{ !$diagnosis->notes ?: 'checked' }}> Sonstiges
                </label>
            </div>
  

            <!-- Notes -->
            <label class="form-label" for="notes">Notizen:</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes', $diagnosis->notes) }}</textarea>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mb-3" style="margin-top:10px;">Aktualisieren</button>
    </form>
</div>


@endsection