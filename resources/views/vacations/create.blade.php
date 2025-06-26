@extends('layouts.app')

@section('title', 'Urlaubsantrag einreichen')

@section('content')


    <h1>Urlaubsantrag stellen</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
           <ul>
               @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
               @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vacation.store') }}" method="POST" id="create_task_form">
        @csrf

        <input type='hidden' id='user_id' name='user_id' value="{{auth()->user()->id}}" >
        <!-- Aufgaben-Titel -->
        <div class="mb-3">
            <label for="title" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" readonly>
            @if ($errors->has('name'))
                <div class="text-danger">{{ $errors->first('name') }}</div>
            @endif
        </div>

        <!-- Beschreibung -->
        <div class="mb-3">
            <label for="description" class="form-label">Zusätzliche Informationen</label>
            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
            @if ($errors->has('description'))
                <div class="text-danger">{{ $errors->first('description') }}</div>
            @endif
        </div>

        <label for="start_date" class="form-label">Urlaub Anfang:</label>
        <input 
            type="date" 
            id="start_date" 
            name="start_date" 
            class="form-control form-control-md" 
            value="{{old('start_date')}}"
            placeholder="Select Start Date" 
            min="{{ \Carbon\Carbon::tomorrow()->toDateString() }}">

        <label for="end_date" class="form-label mt-3">Urlaub Ende:</label>
        <input 
            type="date" 
            id="end_date" 
            name="end_date" 
            value="{{old('end_date')}}"
            class="form-control form-control-md" 
            placeholder="Select End Date" 
            min="{{ \Carbon\Carbon::tomorrow()->toDateString() }}">

        <p class="mt-3">Anzahl genutzter Urlaubstage: <span id="dayCount">0</span></p>

        <div class="mb-3">
            <label for="title" class="form-label">Urlaubstage verbleibend:</label>
            <input type="text" class="form-control" value="{{ old('name', auth()->user()->vacation_days) }}" readonly>
        </div>
        
        <!-- Speichern -->
        <button type="submit" class="btn btn-success">Speichern</button>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const dayCountSpan = document.getElementById('dayCount');

        // Prevent past dates
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const minDate = tomorrow.toISOString().split('T')[0];

        startDateInput.setAttribute('min', minDate);
        endDateInput.setAttribute('min', minDate);

        // Update the minimum date for the end date based on the selected start date
        startDateInput.addEventListener('change', function () {
            const selectedStartDate = new Date(startDateInput.value);

            if (startDateInput.value) {
                // Set the minimum date for the end date
                const minEndDate = new Date(selectedStartDate);
                minEndDate.setDate(selectedStartDate.getDate() + 1); // Ensure it's after the start date
                endDateInput.setAttribute('min', minEndDate.toISOString().split('T')[0]);

                // Clear the end date if it doesn't meet the new minimum
                if (new Date(endDateInput.value) <= selectedStartDate) {
                    endDateInput.value = '';
                    dayCountSpan.textContent = 0;
                }
            }
        });

        // Calculate the difference in weekdays (exclude Saturdays and Sundays)
        const calculateDays = () => {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate && endDate && endDate > startDate) {
                let dayCount = 0;

                for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
                    // Check if the day is not Saturday (6) or Sunday (0)
                    if (date.getDay() !== 0 && date.getDay() !== 6) {
                        dayCount++;
                    }
                }

                dayCountSpan.textContent = dayCount;
            } else {
                dayCountSpan.textContent = 0;
            }
        };

        startDateInput.addEventListener('change', calculateDays);
        endDateInput.addEventListener('change', calculateDays);
    });
</script>
@endsection
