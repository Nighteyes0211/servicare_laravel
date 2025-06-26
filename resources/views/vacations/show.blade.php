@extends('layouts.app')

@section('title', 'Urlaubsantrag')

@section('content')
    <h1>Urlaubsantrag von {{ $vacation->user->name }}</h1>

    <form action="{{ route('vacation.updateaction') }}" method="POST" id="create_task_form">
        @csrf

        <input type='hidden' id='vacation_id' name='vacation_id' value="{{$vacation->id}}" >
        <!-- Aufgaben-Titel -->
        <div class="mb-3">
            <label for="title" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $vacation->user->name }}" readonly>
        </div>

        <!-- Beschreibung -->
        <div class="mb-3">
            <label for="description" class="form-label">Informationen:</label>
            <textarea class="form-control" id="description" name="description" rows="4" readonly>{{ $vacation->description }}</textarea>
        </div>

        <label for="start_date" class="form-label">Urlaub Anfang:</label>
        <input 
            type="date" 
            id="start_date" 
            name="start_date" 
            class="form-control form-control-md" 
            placeholder="Select Start Date" 
            value="{{ $vacation->vacation_start_date }}" readonly>

        <label for="end_date" class="form-label mt-3">Urlaub Ende:</label>
        <input 
            type="date" 
            id="end_date" 
            name="end_date" 
            class="form-control form-control-md" 
            placeholder="Select End Date" 
            value="{{ $vacation->vacation_end_date }}" readonly>

        <label for="end_date" class="form-label mt-3">Anzahl genutzter Urlaubstage:</label>
        <input 
            type="text" 
            id="end_date" 
            name="end_date" 
            class="form-control form-control-md" 
            placeholder="Select End Date" 
            value="{{ $vacation->number_of_vacations }}" readonly>

        <div class="mb-3">
            <label for="title" class="form-label">Urlaubstage verbleibend:</label>
            <input type="text" class="form-control" value="{{ old('vacation_days', $vacation->user->vacation_days ) }}" readonly>
        </div>
        
        @if (auth()->user()->isAdmin())
        <!-- Kunden-Auswahl -->
        <div class="mb-3">
            <label for="leave_action" class="form-label">Action for Leave Request</label>
            <select name="leave_action" id="leave_action" class="form-control" required>
                <option value="">Bitte auswählen</option>
                <option value="approve" >Genehmigt </option>
                <option value="reject" >Abgelehnt </option>
            </select>
        </div>
        <!-- Speichern -->
        <button type="submit" class="btn btn-success">Speichern</button>
        @endif
@endsection
