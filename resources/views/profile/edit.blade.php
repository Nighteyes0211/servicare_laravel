@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Profil bearbeiten</h2>

        <hr>
    </div>

    <div class=" p-4 mb-4">
        <h5 class="text-primary mb-3">Profil Informationen</h5>

        <div class="max-w-xl mx-auto">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class=" p-4 mb-4">
        <h5 class="text-primary mb-3">Passwort aktualisieren</h5>

        <div class="max-w-xl mx-auto">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class=" p-4">
        <h5 class="text-primary mb-3">Account löschen</h5>

        <div class="max-w-xl mx-auto">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
