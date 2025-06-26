
@extends('layouts.app')

@section('title', 'Kundenverwaltung')

@section('content')
    <h1>Kundenverwaltung</h1>
    {{-- <a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">Neuen Kunden anlegen</a> --}}

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('customers.create') }}" class="btn btn-primary">Neuen Kunden anlegen</a>

        <form action="{{ route('customers.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Kunden suchen..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary">Suchen</button>
        </form>
    </div>
    
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Firmenname</th>
                <th>Adresse</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->company_name }}</td>
                    <td>{{ $customer->street }}, {{ $customer->postal_code }}, {{ $customer->city }}</td>
                    <td>
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-success btn-sm">Anzeigen</a>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm">Bearbeiten</a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Löschen</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4 pagination">
        {{ $customers->links() }}
    </div>
@endsection

