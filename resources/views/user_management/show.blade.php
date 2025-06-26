@extends('layouts.app')

@section('title', 'Benutzerdetails')

@section('content')
<h1>Benutzerdetails</h1>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $user->name }}</h5>
        <p class="card-text">
            <strong>E-Mail:</strong> {{ $user->email }}<br>
            <strong>Rolle:</strong> {{ $user->role }}<br>
        </p>


        <p><strong>4-Tage-Woche:</strong> 
    <span class="text-secondary">
        @if($user->four_day_week)
            <i class="fas fa-check-circle text-success"></i> Ja
        @else
            <i class="fas fa-times-circle text-danger"></i> Nein
        @endif
    </span>
</p>


        <h5 class="card-title">Urlaubshistorie:</h5>
            <ul>
            @foreach($user_vacation as $myvacation)
                    
                <li>{{ $myvacation->vacation_start_date }} - {{ $myvacation->vacation_end_date }} | @if($myvacation->vacation_status == 'Pending') <strong>In Prüfung</strong> @elseif ($myvacation->vacation_status == 'approve') <strong>Genehmigt</strong> @elseif ($myvacation->vacation_status == 'reject') <strong>Abgelehnt</strong> @endif | {{ $myvacation->number_of_vacations }}  Tage genutzt</li>

            @endforeach
            </ul>

        <h5 class="card-title">Urlaubstage verbleibend:</h5>
        <p class="card-text">{{ $user->vacation_days }}</p>
    
      
        <a href="{{ route('user_management.index') }}" class="btn btn-secondary">Zurück zur Liste</a>
    </div>
</div>
@endsection
