@extends('layouts.app')

@section('title', 'Urlaubsantrag')

@section('content')
    
       {{--<!-- <input type='hidden' id='vacation_id' name='vacation_id' value="{{$vacation->id}}" >-->

        <!-- Beschreibung -->
        <!-- <div class="mb-3">
            <label for="description" class="form-label">Informationen</label>
            <textarea class="form-control" id="description" name="description" rows="4" readonly>{{ $vacation->description }}</textarea>
        </div> -->

        <!-- <label for="start_date" class="form-label">Urlaub Anfang:</label>
        <input 
            type="date" 
            id="start_date" 
            name="start_date" 
            class="form-control form-control-md" 
            placeholder="Select Start Date" 
            value="{{ $vacation->vacation_start_date }}" readonly> -->

        <!-- <label for="end_date" class="form-label mt-3">Urlaub Ende:</label>
        <input 
            type="date" 
            id="end_date" 
            name="end_date" 
            class="form-control form-control-md" 
            placeholder="Select End Date" 
            value="{{ $vacation->vacation_end_date }}" readonly> -->

        <!-- <label for="end_date" class="form-label mt-3">Anzahl genutzter Urlaubstage:</label>
        <input 
            type="text" 
            id="end_date" 
            name="end_date" 
            class="form-control form-control-md" 
            placeholder="Select End Date" 
            value="{{ $vacation->number_of_vacations }}" readonly> -->

        <!-- <div class="mb-3">
            <label for="title" class="form-label">Urlaubstage verbleibend:</label>
            <input type="text" class="form-control" value="{{ old('vacation_days', $vacation->user->vacation_days ) }}" readonly>
        </div> -->--}}

        <h3>{{ $user->surname }} {{ $user->name }}</h3>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <!-- <div class="card-body"> -->
        <h5 class="card-title">Informationen</h5>

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
            <p class="card-text">Noch keinen Urlaub genommen.</p>
        @endif--}}

        <h5 class="card-title">Vergangene Urlaubstage:</h5>
            <ul>
            @foreach($user_vacation as $myvacation)
                    
                <li>{{ $myvacation->vacation_start_date }} - {{ $myvacation->vacation_end_date }} | @if($myvacation->vacation_status == 'Pending') <strong>In Prüfung</strong> @elseif ($myvacation->vacation_status == 'approve') <strong>Genehmigt</strong> @elseif ($myvacation->vacation_status == 'reject') <strong>Abgelehnt</strong> @endif | {{ $myvacation->number_of_vacations }}  Tage genutzt</li>

            @endforeach
            </ul>

        <h5 class="card-title">Urlaubstage verbleibend:</h5>
        <p class="card-text">{{ $user->vacation_days }}</p>

        
@endsection
