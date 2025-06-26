# Planner Modal für Aufgabe / Details

@php
    $statusLabels = [
        'open' => 'Offen',
        'done' => 'Erledigt',
        'not_done' => 'Nicht erledigt',
        'billed' => 'Abgerechnet',
        'pending' => 'In Bearbeitung',
        'completed' => 'Abgeschlossen',
    ];
@endphp


<div class="p-4 mb-4">
    <h5 class="h4 text-primary mb-3">Aufgabe Details</h5>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-3">
        <div class="col-md-6">
            <p><strong>Mitarbeiter:</strong> <span class="text-secondary">{{ $task->user->name }}</span></p>
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
            <p><strong>Aufgabentitel:</strong> <span class="text-secondary">{{ $task->title }}</span></p>
        </div>
        <div class="col-md-6">
            <p><strong>Status:</strong> 
                <span class="badge {{ $task->status == 'completed' ? 'bg-success' : ($task->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                    {{ $statusLabels[$task->status] ?? ucfirst($task->status) }}
                </span>
            </p>
            <p><strong>Fälligkeitsdatum:</strong> 
                <span class="text-secondary">
                    {{ $task->due_date ? $task->due_date->format('d.m.Y') : 'Kein Fälligkeitsdatum angegeben' }}
                </span>
            </p>
        </div>
    </div>
    <div class="mb-4">
        <p><strong>Beschreibung:</strong></p>
        <p class="text-secondary">{{ $task->comment ?? 'Keine Beschreibung vorhanden' }}</p>
    </div>



    TEST
    
    @if ($task->pdf_path)
    <div class="mb-3">
        <p><strong>Download PDF:</strong> 
            <a href="{{ asset($task->pdf_path) }}" class="btn btn-success" download>Download PDF</a>
        </p>
    </div>
    @endif

    <div class="d-flex justify-content-between mt-4">
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Zurück zur Übersicht
    </a>

    @if(auth()->user()?->isAdmin())
        <button class="btn btn-primary open-edit-modal" data-id="{{ $task->id }}">
            <i class="fas fa-edit me-2"></i>Bearbeiten
        </button>
        <button class="btn btn-danger delete-task" data-id="{{ $task->id }}">
            <i class="fas fa-trash-alt me-2"></i>Löschen
        </button>
    @endif
</div>
    
</div>

<script>
    $(document).on('click', '.delete-task', function () {
    var taskId = $(this).data('id');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    if (confirm('Bist du sicher, dass du diese Aufgabe löschen möchtest?')) {
        $.ajax({
            url: '/tasks/' + taskId,
            type: 'DELETE',
            data: { _token: csrfToken },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (response) {
                alert(response.message);
                $('#taskModal').modal('hide');
                location.reload();
            },
            error: function (xhr, status, error) {
                alert('Fehler beim Löschen der Aufgabe.');
                console.error(xhr.responseText);
            }
        });
    }
});
</script>