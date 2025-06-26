@extends('layouts.app')

@section('title', 'Ansprechpartner hinzufügen')

@section('content')
    <h1>Neuen Ansprechpartner hinzufügen für {{ $customer->name }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contacts.store', $customer) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="salutation">Anrede</label>
            <select name="salutation" class="form-control">
                <option value="Herr">Herr</option>
                <option value="Frau">Frau</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="first_name">Vorname</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}">
        </div>

        <div class="mb-3">
            <label for="last_name">Nachname</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
        </div>

        <div class="mb-3">
            <label for="email">E-Mail</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="phone">Telefon</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>

        <div class="mb-3">
            <label for="mobile">Mobil</label>
            <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
        </div>

        <div class="mb-3">
            <label for="position">Position</label>
            <input type="text" name="position" class="form-control" value="{{ old('position') }}">
        </div>

        <button type="submit" class="btn btn-success">Speichern</button>
    </form>
@endsection
