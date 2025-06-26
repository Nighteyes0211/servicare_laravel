@extends('layouts.app')

@section('title', 'Kundendetails')

@section('content')
    <h1>Kundendetails</h1>

    <!-- Kundendetails -->
    <div class="card shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-primary">{{ $customer->company_name }}</h1>
            <!--<a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Bearbeiten
            </a>-->
        </div>

        <div class="mb-3">
            <p><strong>Firmenname:</strong> <span class="text-secondary">{{ $customer->company_name }}</span></p>
            <p><strong>Straße:</strong> <span class="text-secondary">{{ $customer->street }}</span></p>
            <p><strong>PLZ:</strong> <span class="text-secondary">{{ $customer->postal_code }}</span></p>
            <p><strong>Stadt:</strong> <span class="text-secondary">{{ $customer->city }}</span></p>
            <p><strong>Land:</strong> <span class="text-secondary">{{ $customer->country ?? 'Nicht angegeben' }}</span></p>
            <p><strong>Telefon:</strong> <span class="text-secondary">{{ $customer->phone ?? 'Nicht angegeben' }}</span></p>
            <p><strong>E-Mail:</strong> <span class="text-secondary">{{ $customer->email }}</span></p>
        </div>

        <div class="text-end">
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> Zurück zur Übersicht
            </a>
        </div>
    </div>


    <div class="card shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-primary">Ansprechpartner</h1>
            <a href="{{ route('contacts.create', $customer) }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-2"></i> Ansprechpartner hinzufügen
            </a>
        </div>

        @if (!empty($customer) && !empty($customer->contacts) && $customer->contacts->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th>Anrede</th>
                            <th>Vorname</th>
                            <th>Nachname</th>
                            <th>Telefon</th>
                            <th>Mobil</th>
                            <th>E-Mail</th>
                            <th>Position</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->contacts as $contact)
                            <tr>
                                <td>{{ $contact->salutation }}</td>
                                <td>{{ $contact->first_name }}</td>
                                <td>{{ $contact->last_name }}</td>
                                <td>{{ $contact->phone ?? 'Nicht angegeben' }}</td>
                                <td>{{ $contact->mobile ?? 'Nicht angegeben' }}</td>
                                <td>{{ $contact->email ?? 'Nicht angegeben' }}</td>
                                <td>{{ $contact->position ?? 'Nicht angegeben' }}</td>
                                <td>
                                    <form action="{{ route('contacts.destroy', ['customer' => $customer->id, 'contact' => $contact->id])  }}" method="POST" onsubmit="return confirm('Wirklich löschen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Löschen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">Keine Ansprechpartner vorhanden.</p>
        @endif
    </div>


    <div class="card shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-primary">Aufträge</h1>
        </div>

        @if ($tasks->count())
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Titel</th>
                            <th>Startzeit</th>
                            <th>Endzeit</th>
                            <th>Status</th>
                            <th>Abgerechnet am</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->start_time)->translatedFormat('d. F Y, H:i \U\h\r') }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->end_time)->translatedFormat('d. F Y, H:i \U\h\r') }}</td>
                                
                                <td>
                                    <span class="badge bg-{{ $task->status === 'open' ? 'warning' : 'success' }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>

                                <td>{{ \Carbon\Carbon::parse($task->due_date)->translatedFormat('d F Y') }}</td>

                                <td> <a class="btn btn-success btn-sm" href="{{ route('tasks.show', $task) }}">Anzeigen</a></td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $tasks->links() }}
            </div>
        @else
            <p class="text-muted">Für diesen Kunden sind keine Aufträge verfügbar.</p>
        @endif
    </div>



    </div>
@endsection
