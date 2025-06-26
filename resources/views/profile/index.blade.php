@extends('layouts.app')

@section('title', 'Mein Profil')

@section('content')
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Mein Profil</h2>
        <p class="text-muted">Hier können Sie Ihre Kontodaten einsehen und verwalten.</p>
        <hr>
    </div>

    <div class=" p-4">
        <h5 class="text-primary mb-3">Profil Informationen</h5>

        <div class="mb-3">
            <label class="form-label fw-bold">Name</label>
            <div>{{ $user->name }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">E-Mail</label>
            <div>{{ $user->email }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Erstellt am:</label>
            <div>{{ $user->created_at->format('F j, Y') }}</div>
        </div>

        <div class="mt-4">
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">Profil bearbeiten</a>
        </div>

        @if(isset($activities) && count($activities) > 0)
            <hr class="my-4">
            <h5 class="text-primary mb-3">Recent Activity</h5>
            <ul class="list-group list-group-flush">
                @foreach($activities as $activity)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $activity->description }}</span>
                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
