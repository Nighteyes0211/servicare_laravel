@extends('layouts.app')

@section('title', 'Diagnosebogen Details')
@section('content')

<div class="card shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary">Diagnosebogen Details</h1>
        <a href="{{ route('diagnoses.edit', $diagnosis) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Bearbeiten
        </a>    
    </div>
    
    <div class="mb-3">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <th class="text-secondary">Kunde</th>
                    <td>{{ $diagnosis->customer->company_name }}</td>
                </tr>
                <tr>
                    <th class="text-secondary">Telefon</th>
                    <td>{{ $diagnosis->phone }}</td>
                </tr>
                <tr>
                    <th class="text-secondary">Diagnosedetails</th>
                    <td>{{ $diagnosis->diagnosis_details }}</td>
                </tr>
                <tr>
                    <th class="text-secondary">Diagnosedatum</th>
                    <td>{{ $diagnosis->diagnosis_date }}</td>
                </tr>
                <tr>
                    <th class="text-secondary">Diagnosetyp</th>
                    <td>
                        @if($diagnosis->type)
                            {{ implode(', ', explode(',', $diagnosis->type)) }}
                        @else
                            Kein Diagnosetyp vorhanden
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="text-secondary">Aktion(en)</th>
                    <td>{{ is_array(json_decode($diagnosis->action)) ? implode(', ', json_decode($diagnosis->action)) : $diagnosis->action }}</td>
                </tr>
                <tr>
                    <th class="text-secondary">Weitergeleitet an</th>
                    <td>{{ $diagnosis->forwarded_to }}</td>
                </tr>
                <tr>
                    <th class="text-secondary">Erstellt am</th>
                    <td>{{ $diagnosis->created_at->format('d.m.Y H:i') }}</td>
                </tr>
                <tr>
                    <th class="text-secondary">Aktualisiert am</th>
                    <td>{{ $diagnosis->updated_at->format('d.m.Y H:i') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex gap-3">
       
        <a href="{{ route('diagnoses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Zurück zur Übersicht
        </a>
    </div>
</div>

@endsection
