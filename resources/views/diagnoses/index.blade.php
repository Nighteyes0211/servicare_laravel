@extends('layouts.app')

@section('title', 'Diagnose und Aufnahme')
@section('content')

<h1>Diagnosebögen</h1>
<a href="{{ route('diagnoses.create') }}" class="btn btn-primary mb-3">Neuen Diagnosebogen erstellen</a>
<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Diagnose ID</th>
            <th>Kunde</th>
            <th>Diagnosedetails</th>
            <th>Diagnosedatum</th>
            <th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
        @foreach($diagnoses as $diagnosis)
        <tr>
            <td>{{ $diagnosis->id }}</td>
            <td>{{ $diagnosis->customer->company_name }}</td>
            <td>{{ $diagnosis->diagnosis_details }}</td>
            <td>{{ $diagnosis->diagnosis_date }}</td>
            <td>
                <a href="{{ route('diagnoses.show', $diagnosis) }}" class="btn btn-success btn-sm">Anzeigen</a>
                <a href="{{ route('diagnoses.edit', $diagnosis) }}" class="btn btn-primary btn-sm">Bearbeiten</a>
                <form action="{{ route('diagnoses.destroy', $diagnosis) }}" btn-sm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" type="submit">Löschen</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>



@endsection