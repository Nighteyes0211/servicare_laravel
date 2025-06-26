@extends('layouts.app')

@section('title', 'Urlaubsantrag')

@section('content')
    
        

        <h3>{{ $vacation->user->surname }} {{ $vacation->user->name }}</h3>
    <p><strong>Email:</strong> {{ $vacation->user->email }}</p>
    <!-- <div class="card-body"> -->
        <h5 class="card-title">Informationen</h5>
        <p class="card-text">{{ $vacation->description ?? 'No description available.' }}</p>

        {{--@if($vacation->user->vacations && $vacation->user->vacations->isNotEmpty())
            @foreach ($vacation->user->vacations as $history)
                <h5 class="card-title">{{ \Carbon\Carbon::parse($history->vacation_start_date)->format('Y') }}:</h5>
                <p class="card-text">
                    {{ \Carbon\Carbon::parse($history->vacation_start_date)->format('d.m.Y') }} - 
                    {{ \Carbon\Carbon::parse($history->vacation_end_date)->format('d.m.Y') }} 
                    | Used for {{ $history->number_of_vacations }} days
                </p>
            @endforeach
        @else
            <p class="card-text">No Urlaubshistorie available.</p>
        @endif--}}

        <h5 class="card-title">Urlaubshistorie:</h5>
            <ul>
            @foreach($user_vacation as $myvacation)
                    
                <li>{{ $myvacation->vacation_start_date }} - {{ $myvacation->vacation_end_date }} | @if($myvacation->vacation_status == 'Pending') <strong>In Prüfung</strong> @elseif ($myvacation->vacation_status == 'approve') <strong>Genehmigt</strong> @elseif ($myvacation->vacation_status == 'reject') <strong>Abgelehnt</strong> @endif | {{ $myvacation->number_of_vacations }}  Tage genutzt</li>

            @endforeach
            </ul>

        <h5 class="card-title">Urlaubstage verbleibend:</h5>
        <p class="card-text">{{ $vacation->user->vacation_days }}</p>

        
@endsection
