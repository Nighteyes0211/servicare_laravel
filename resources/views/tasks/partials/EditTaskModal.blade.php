

<form id="editTaskForm" data-action="{{ route('tasks.update', $task->id) }}">


    @csrf


    <input type="hidden" name="task_id" value="{{ $task->id }}">

    <div class="mb-3 task-title-field">
        <label for="title">Titel</label>
        <input type="text" name="title" value="{{ $task->title }}" class="form-control">
    </div>

    <div class="mb-3">
        <label for="user_id">Mitarbeiter</label>
        <select name="user_id" class="form-control">
            <option value="">Bitte wählen</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $task->user_id == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3 customer-field">
        <label for="customer_id">Kunde</label>
        <select name="customer_id" class="form-control" required>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" {{ $task->customer_id == $customer->id ? 'selected' : '' }}>
                    {{ $customer->company_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3 status-field">
        <label for="status">Status</label>
        <select name="status" class="form-control" required>
            <option value="open" {{ $task->status == 'open' ? 'selected' : '' }}>Offen</option>
            <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Erledigt</option>
            <option value="not_done" {{ $task->status == 'not_done' ? 'selected' : '' }}>Nicht erledigt</option>
            <option value="billed" {{ $task->status == 'billed' ? 'selected' : '' }}>Abgerechnet</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="start_time">Startdatum</label>
        <input type="date" name="start_time" value="{{ $task->start_time?->format('Y-m-d') }}" class="form-control">
    </div>

    <div class="mb-3">
        <label for="end_time">Enddatum</label>
        <input type="date" name="end_time" value="{{ $task->end_time?->format('Y-m-d') }}" class="form-control">
    </div>


    <div class="mb-3">
        <label for="comment">Beschreibung</label>
        <textarea name="comment" class="form-control">{{ $task->comment }}</textarea>
    </div>

    <div class="mb-3">
        <label>Kategorien</label>
        @foreach ($categories as $category)
            <div class="form-check">
                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                    class="form-check-input"
                    {{ $task->categories->contains($category->id) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-success">Speichern</button>
</form>

<script>
    function toggleFields() {
        const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
            .map(cb => parseInt(cb.value));

        const urlaubId = @json(\App\Models\TaskCategory::where('name', 'Urlaub')->first()?->id);
        const krankId = @json(\App\Models\TaskCategory::where('name', 'Krank')->first()?->id);

        const isSpecial = selectedCategories.includes(urlaubId) || selectedCategories.includes(krankId);

        const titleField = document.querySelector('.task-title-field');
        const customerField = document.querySelector('.customer-field');
        const statusField = document.querySelector('.status-field');

        if (isSpecial) {
            titleField.style.display = 'none';
            customerField.style.display = 'none';
            statusField.style.display = 'none';

            document.querySelector('[name="title"]')?.removeAttribute('required');
            document.querySelector('[name="customer_id"]')?.removeAttribute('required');
            document.querySelector('[name="status"]')?.removeAttribute('required');
        } else {
            titleField.style.display = '';
            customerField.style.display = '';
            statusField.style.display = '';

            document.querySelector('[name="title"]')?.setAttribute('required', 'required');
            document.querySelector('[name="customer_id"]')?.setAttribute('required', 'required');
            document.querySelector('[name="status"]')?.setAttribute('required', 'required');
        }
    }

    // 🔁 Nach dem Laden des Modals sofort aufrufen
    toggleFields();

    // 🔄 Event Listener neu setzen
    document.querySelectorAll('input[name="categories[]"]').forEach(cb => {
        cb.addEventListener('change', toggleFields);
    });
</script>
