@extends('layouts.app')

@section('title', 'Auftragsverwaltung')
@section('content')

<style>
    .small-text-table {
        font-size: 15px;
    }
</style>




<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="m-0">Auftragsverwaltung</h1>
    @if (auth()->user() && auth()->user()->isAdmin())
    <a class="btn btn-primary" href="{{ route('tasks.create') }}">
        Auftrag erstellen
    </a>
    @endif
</div>
<form action="{{ route('tasks.index') }}" method="GET" class="row g-2 mb-3 align-items-end">
    <div class="col-md-4">
        <label for="search" class="form-label">Suche</label>
        <input type="text" name="search" id="search" class="form-control" placeholder="Titel, Kunde, Beschreibung, Mitarbeiter" value="{{ request('search') }}">
    </div>

    <div class="col-md-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-control">
            <option value="">Alle</option>
            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Offen</option>
            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Erledigt</option>
              @if(auth()->user()?->isAdmin())
            <option value="not_done" {{ request('status') == 'not_done' ? 'selected' : '' }}>Nicht erledigt</option>
            <option value="billed" {{ request('status') == 'billed' ? 'selected' : '' }}>Abgerechnet</option>
            @endif
        </select>
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-primary w-100">Filtern</button>
    </div>
</form>





@php
    $successMsg = session()->get('success');
    session()->forget('success');
@endphp
@if ($successMsg)
    <div class="alert alert-success">
        {{ $successMsg }}
    </div>
@endif

@php
    $errorMsgs = session()->get('error');
    session()->forget('error');
@endphp
@if ($errorMsgs)
    <div class="alert alert-danger">
        {{ $errorMsgs }}
    </div>
@endif



<table class="table table-striped table-hover small-text-table">

    <thead class="table-dark">
        <tr>
            <th>Mitarbeiter</th>
            <th>Kunde</th>
            <th>Ort</th> <!-- NEU -->
            <th>Beschreibung</th> <!-- NEU -->
            <th>Status</th>
            <th>Auftragsnummer</th>
            <th>Aktionen</th>
            @php
    $currentSort = request('sort');
    $newSort = ($currentSort === 'created_at_asc') ? 'created_at_desc' : 'created_at_asc';
    $arrow = $currentSort === 'created_at_asc' ? '↑' : ($currentSort === 'created_at_desc' ? '↓' : '');
@endphp

<th style="white-space: nowrap;">
    <a href="{{ route('tasks.index', array_merge(request()->all(), ['sort' => $newSort])) }}">
        Erstellt am {!! $arrow !!}
    </a>
</th>
            @php
    $currentSort = request('sort');
    $newSortUpdated = ($currentSort === 'updated_at_asc') ? 'updated_at_desc' : 'updated_at_asc';
    $arrowUpdated = $currentSort === 'updated_at_asc' ? '↑' : ($currentSort === 'updated_at_desc' ? '↓' : '');
@endphp

<th style="white-space: nowrap;">
    <a href="{{ route('tasks.index', array_merge(request()->all(), ['sort' => $newSortUpdated])) }}">
        Bearbeitet am {!! $arrowUpdated !!}
    </a>
</th>
        </tr>
    </thead>
    <tbody>
    @foreach($tasks as $task)
        <tr>
            <td>
                @if ($task->user)
                    {{ $task->user->name }}
                @else
                    <span class="text-muted">Nicht zugeordnet</span>
                @endif
            </td>

            <td>{{ $task->customer->company_name ?? 'Nicht zugeordnet' }}</td>

            <td>{{ $task->customer->city ?? '-' }}</td> <!-- ORT -->

            <td>{{ \Illuminate\Support\Str::limit($task->comment, 50) ?? '-' }}</td> <!-- BESCHREIBUNG -->

            <td>
                <span class="badge bg-{{ $task->status === 'open' ? 'warning' : 'success' }}">
                    {{ $task->status_de }}
                </span>
            </td>

            <td>{{ $task->order_number ?? '-' }}</td>

            <td class="text-nowrap">
    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary" title="Anzeigen">
        <i class="fas fa-eye"></i>
    </a>
    @if (auth()->user() && auth()->user()->isAdmin())
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary" title="Bearbeiten">
            <i class="fas fa-edit"></i>
        </a>
    @endif
    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="delete-task-form" style="display:inline;" onsubmit="return false;">
    @csrf
    @method('DELETE')
    @if (auth()->user() && auth()->user()->isAdmin())
        <button class="btn btn-sm btn-outline-danger" title="Löschen">
            <i class="fas fa-trash-alt"></i>
        </button>
    @endif
</form>
</td>

            <td style="white-space: nowrap;">{{ $task->created_at->format('d.m.Y H:i') }}</td>
<td style="white-space: nowrap;">{{ $task->updated_at->format('d.m.Y H:i') }}</td>
        </tr>
    @endforeach
</tbody>
</table>

<div class="d-flex justify-content-center mt-4 pagination">
    {{ $tasks->links() }}
</div>


<script>
    $(document).on('submit', '.delete-task-form', function (e) {
        e.preventDefault(); // Verhindert normales Absenden

        if (!confirm('Wirklich löschen?')) return;

        let form = $(this);
        let url = form.attr('action');
        let row = form.closest('tr');

        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(), // enthält CSRF-Token und _method
            success: function(response) {
                row.remove(); // Zeile aus Tabelle entfernen
                alert(response.message);
            },
            error: function(xhr) {
                alert('Fehler beim Löschen');
            }
        });
    });
</script>


@endsection




