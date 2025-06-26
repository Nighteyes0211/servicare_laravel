@extends('layouts.app')

@section('title', 'Urlaubsanträge')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="m-0">Urlaubsanträge</h1>
    <a class="btn btn-primary" href="{{ route('vacation.create') }}">
    Urlaubsantrag einreichen
</a>
</div>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<table class="table table-striped table-hover">
        <thead class="table-dark">
        <tr>
            <th>Mitarbeiter</th>
            <th>Datum Urlaub Beginn</th>
            <th>Datum Urlaub Ende</th>
            <!-- <th>Anzahl der beantragten Urlaube</th> -->
            <th>Status</th>
            <th>Datum Urlaubsantrag</th>

            <th>Genehmigt von</th>
            <th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vacations as $vacation)
            <tr>
                <td>{{ $vacation->user->name }}</td>
                <td>{{ $vacation->vacation_start_date }}</td>
                <td>{{ $vacation->vacation_end_date }}</td>
               <!-- <td>{{ $vacation->number_of_vacations }}</td> -->
                <td>@if($vacation->vacation_status == 'Pending') <strong>In Prüfung</strong> @elseif ($vacation->vacation_status == 'approve') <strong>Genehmigt</strong> @elseif ($vacation->vacation_status == 'reject') <strong>Abgelehnt</strong> @endif</td>
                <td>{{ $vacation->apply_date }}</td>
                <td>@if($vacation->approved_by) {{ $vacation->approve->name }} @else @endif</td>
                <td>
                @if(auth()->user()->isAdmin())
                    <a class="btn btn-success btn-sm" href="{{ route('vacation.show', $vacation) }}">Bearbeiten</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('vacationAdmin.show', $vacation) }}">Anzeigen</a>
                @else
                    <a class="btn btn-success btn-sm" href="{{ route('vacation.show', $vacation) }}">Anzeigen</a>
                    @if ($vacation->approved_by)
                    @else
                    <a class="btn btn-primary btn-sm" href="{{ route('vacation.edit', $vacation) }}" class="btn btn-primary btn-sm">Bearbeiten</a>
                    <form action="{{ route('vacation.destroy', $vacation) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" class="btn btn-danger btn-sm" type="submit">Löschen</button>
                    </form>
                    @endif
                @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


@endsection
