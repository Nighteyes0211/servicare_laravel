

@extends('layouts.app')

@section('title', 'Auftrag anzeigen')
@section('content')
<div class="card shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary">Aufgabe Details</h1>
        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary" data-id="{{$task->id}}">
            <i class="fas fa-edit me-2"></i>Bearbeiten
        </a>
    </div>
    
    <div class="mb-3">
        <p><strong>Auftragsnummer:</strong> <span class="text-secondary">{{ $task->order_number ?? 'Keine Auftragsnummer vorhanden' }}</span></p>
        @if($task->customer)
    <p><strong>Kunde:</strong> <span class="text-secondary">{{ $task->customer->company_name }}</span></p>
    @if($task->customer)
    <p><strong>Adresse:</strong> 
        <span class="text-secondary">
            {{ $task->customer->street }}, 
            {{ $task->customer->postal_code }} {{ $task->customer->city }}
        </span>
        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($task->customer->street . ', ' . $task->customer->postal_code . ' ' . $task->customer->city) }}" 
           class="btn btn-outline-primary btn-sm ms-2" 
           target="_blank" 
           title="In Google Maps öffnen">
            <i class="fas fa-map-marked-alt"></i> Karte
        </a>
    </p>
@endif
    <p><strong>Land:</strong> <span class="text-secondary">{{ $task->customer->country ?? '–' }}</span></p>
    <p><strong>Telefon:</strong> <span class="text-secondary">{{ $task->customer->phone ?? '–' }}</span></p>
    <p><strong>E-Mail:</strong> <span class="text-secondary">{{ $task->customer->email }}</span></p>
@else
    <p><strong>Kunde:</strong> <span class="text-danger">Kein Kunde zugewiesen</span></p>
@endif
        <p><strong>Mitarbeiter:</strong> @if ($task->user)
    {{ $task->user->name }}
@else
    <span class="text-muted">Kein Mitarbeiter zugeordnet</span>
@endif</p>
        <p><strong>Aufgabentitel:</strong> <span class="text-secondary">{{ $task->title }}</span></p>
        <p><strong>Beschreibung:</strong> <span class="text-secondary">{{ $task->comment ?? 'Keine Beschreibung vorhanden' }}</span></p>
        <p><strong>Status:</strong> 
            <span class="badge bg-{{ $task->status === 'open' ? 'warning' : 'success' }}">
                                         {{ translateTaskStatus($task->status) }}
                                    </span>
        </p>
        <p><strong>Fälligkeitsdatum:</strong> 
            <span class="text-secondary">
                {{ $task->due_date ? $task->due_date->format('d.m.Y') : 'Kein Fälligkeitsdatum angegeben' }}
            </span>
        </p>



        @if ($task->pdf_path)
    <p><strong>Auftrag PDF:</strong> 
        <a href="{{ asset($task->pdf_path) }}" target="_blank" class="btn btn-info">
            📄 Auftrag herunterladen
        </a>
    </p>

    @if($task->pdf_path != '' && $task->pdf_path != null)
        <div class="mb-3">
            <label class="form-label">Servicebericht Both & Wandless</label>
            <ul id='ServiceList'>
                @foreach($task_service_forms as $first_form)
                    <li>
                        {{ $first_form->first_service_file }} |
                        <a href="{{ asset($first_form->first_service_file) }}" target="_blank">Bericht anzeigen</a>
                        @if(!empty($first_form->first_services_id))
                            | <a href="{{ route('service-records.destroy', $first_form->first_services_id) }}"
                                 onclick="return confirm('Möchtest du den Bericht wirklich löschen?')"
                                 class="text-danger">Bericht löschen</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-3">
        <label class="form-label">Bericht Wissner</label>
        <ul id='secondServiceList'>
            @foreach($task_second_service_forms as $second_form)
                <li>
                    {{ $second_form->second_service_file }} |
                    <a href="{{ asset($second_form->second_service_file) }}" target="_blank">Show PDF</a>
                    @if(!empty($second_form->service_id))
                        | <a href="{{ route('second-service-records.destroy', $second_form->service_id) }}"
                             onclick="return confirm('Möchtest du den Bericht wirklich löschen?')"
                             class="text-danger">Bericht löschen</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

@endif

<p><strong>Erstellt am:</strong> 
    <span class="text-secondary">
        {{ $task->created_at ? $task->created_at->format('d.m.Y H:i') : '—' }}
    </span>
</p>

<p><strong>Erstellt von:</strong>
    <span class="text-secondary">
        {{ $task->user?->name ?? 'Nicht zugewiesen' }}
    </span>
</p>

    
    <!-- Fälligkeitsdatum -->
    <input type="text" id='inputFieldId' name='service_report_id' class="form-control" value="{{ old('service_report_id') }}" hidden>
    <input type="text" id='secondInputFieldId' name='second_service_report_id' class="form-control" value="{{ old('service_report_id') }}" hidden>
    

    {{-- <p>No PDF available for download.</p> --}}


    </div>

    <!-- Back to Overview Button -->
    <div class="d-flex justify-content-between">
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Zurück zur Übersicht
        </a>
    </div>
</div>

@endsection