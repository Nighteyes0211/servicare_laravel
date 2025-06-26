<form action="{{ route('tasks.update', $task->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Aufgaben-Titel -->
    <div class="mb-3">
        <label for="title" class="form-label">Aufgabentitel</label>
        <input type="text" class="form-control" id="title" name="title" value="{{ $task->title }}" required>
    </div>

    <!-- Benutzer-Auswahl -->
    <div class="mb-3">
        <label for="user_id" class="form-label">Mitarbeiter</label>
        <select name="user_id" id="user_id" class="form-control" required>
            <option value="">Mitarbeiter auswählen</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $task->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Kunden-Auswahl -->
    <div class="mb-3">
        <label for="customer_id" class="form-label">Kunde</label>
        <select name="customer_id" id="customer_id" class="form-control" required>
            <option value="">Kunde auswählen</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" {{ $task->customer_id == $customer->id ? 'selected' : '' }}>
                    {{ $customer->company_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Kategorien-Auswahl -->
    <div class="mb-3">
        <label for="categories" class="form-label">Aufgabenkategorien</label>
        <div>
            @foreach ($categories as $category)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="categories[]" id="category_{{ $category->id }}" 
                           value="{{ $category->id }}" 
                           {{ in_array($category->id, $task->categories->pluck('id')->toArray()) ? 'checked' : '' }}>
                    <label class="form-check-label" for="category_{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Startzeit -->
    <div class="mb-3">
        <label for="start_time" class="form-label">Startzeit</label>
        <input type="datetime-local" name="start_time" id="start_time" class="form-control" 
               value="{{ $task->start_time ? $task->start_time->format('Y-m-d\TH:i') : '' }}" required>
    </div>

    <!-- Endzeit -->
    <div class="mb-3">
        <label for="end_time" class="form-label">Endzeit</label>
        <input type="datetime-local" name="end_time" id="end_time" class="form-control" 
               value="{{ $task->end_time ? $task->end_time->format('Y-m-d\TH:i') : '' }}" required>
    </div>

    <!-- Beschreibung -->
    <div class="mb-3">
        <label for="comment" class="form-label">Beschreibung</label>
        <textarea class="form-control" id="comment" name="comment" rows="4">{{ $task->description }}</textarea>
    </div>

    <!-- Status -->
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
        </select>
    </div>

    <!-- Fälligkeitsdatum -->
    <div class="mb-3">
        <label for="due_date" class="form-label">Fälligkeitsdatum</label>
        <input type="date" class="form-control" id="due_date" name="due_date" 
               value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
        <button type="submit" class="btn btn-primary">Speichern</button>
    </div>
</form>
