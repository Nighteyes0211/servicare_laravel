

@section('title', 'Aufgabe erstellen')


    <h1>Auftrag erstellen</h1>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        <!-- Aufgaben-Titel -->
        <div class="mb-3">
            <label for="title" class="form-label">Bezeichnung des Auftrags</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            @if ($errors->has('title'))
                <div class="text-danger">{{ $errors->first('title') }}</div>
            @endif
        </div>

        <!-- Benutzer-Auswahl -->
        <div class="mb-3">
            <label for="user_id" class="form-label">Zuständiger Mitarbeiter</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">Mitarbeiter auswählen</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('user_id'))
                <div class="text-danger">{{ $errors->first('user_id') }}</div>
            @endif
        </div>

        <!-- Kunden-Auswahl -->
        <div class="mb-3">
            <label for="customer_id" class="form-label">Kunde</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                <option value="">Kunde auswählen</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->company_name }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('customer_id'))
                <div class="text-danger">{{ $errors->first('customer_id') }}</div>
            @endif
        </div>

        <!-- Kategorien-Auswahl -->
        <div class="mb-3">
            <label for="categories" class="form-label">Art des Auftrags</label>
            <div>
                @foreach ($categories as $category)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="categories[]" id="category_{{ $category->id }}" 
                               value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="category_{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @if ($errors->has('categories'))
                <div class="text-danger">{{ $errors->first('categories') }}</div>
            @endif
        </div>

        <!-- Priority Color Picker -->
        <div class="mb-3">
            <label for="priority" class="form-label">Farbe des Auftrags</label>
            <select name="priority" id="priority" class="form-control" required onchange="updateColorPicker()">
                <option value="#28a745">Grün</option>
                <option value="#ffc107">Gelb</option>
                <option value="#dc3545">Rot</option>
                <option value="#ff8800">Orange</option>
                <option value="#000064">Blau</option>
            </select>
        </div>

        <!-- Beschreibung -->
        <div class="mb-3">
            <label for="comment" class="form-label">Beschreibung</label>
            <textarea class="form-control" id="comment" name="comment" rows="4">{{ old('description') }}</textarea>
            @if ($errors->has('comment'))
                <div class="text-danger">{{ $errors->first('description') }}</div>
            @endif
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="">Bitte wählen</option>
                <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Offen</option>
                <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Erledigt</option>
                <option value="not_done" {{ old('status') == 'not_done' ? 'selected' : '' }}>Nicht erledigt</option>
                <option value="billed" {{ old('status') == 'billed' ? 'selected' : '' }}>Abgerechnet</option>
            </select>
            @if ($errors->has('status'))
                <div class="text-danger">{{ $errors->first('status') }}</div>
            @endif
        </div>

        <!-- Startzeit -->
        <div class="mb-3">
            <label for="start_time" class="form-label">Startzeit</label>
            <input type="datetime-local" name="start_time" id="start_time" class="form-control" 
                   value="{{ old('start_time') }}" required>
            @if ($errors->has('start_time'))
                <div class="text-danger">{{ $errors->first('start_time') }}</div>
            @endif
        </div>

        <!-- Endzeit -->    
        <div class="mb-3">
            <label for="end_time" class="form-label">Endzeit</label>
            <input type="datetime-local" name="end_time" id="end_time" class="form-control" 
                   value="{{ old('end_time') }}" required>
            @if ($errors->has('end_time'))
                <div class="text-danger">{{ $errors->first('end_time') }}</div>
            @endif
        </div>

        <!-- Speichern -->
        <button type="submit" class="btn btn-success">Speichern</button>
        <button type="submit" class="btn btn-primary">Servicebericht Wissner erstellen</button>
    </form>

    @endsection
