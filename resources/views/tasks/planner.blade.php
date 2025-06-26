 @php
    use Carbon\Carbon;
    use App\Models\User;

    function getPublicHolidays($year) {
        return [
            Carbon::create($year, 1, 1),    // Neujahr
            Carbon::create($year, 5, 1),    // Tag der Arbeit
            Carbon::create($year, 10, 3),   // Tag der Deutschen Einheit
            Carbon::create($year, 12, 25),  // 1. Weihnachtstag
            Carbon::create($year, 12, 26),  // 2. Weihnachtstag
            easterSunday($year)->addDays(1), // Ostermontag
            easterSunday($year)->subDays(2), // Karfreitag
            easterSunday($year)->addDays(39), // Christi Himmelfahrt
            easterSunday($year)->addDays(50), // Pfingstmontag
            easterSunday($year)->addDays(60), // Fronleichnam
            Carbon::create($year, 11, 1),    // Allerheiligen
        ];
    }


    $publicHolidayDays = collect(getPublicHolidays(Carbon::parse($selectedDate)->year))
    ->filter(fn($holiday) => $holiday->month === Carbon::parse($selectedDate)->month)
    ->map(fn($holiday) => $holiday->day)
    ->toArray();

    function easterSunday($year) {
        $easter = Carbon::create($year, 3, 21)->addDays(easter_days($year));
        return $easter;
    }

    $urlaubId = $categories->firstWhere('name', 'Urlaub')?->id;
    $montageId = $categories->firstWhere('name', 'Montage')?->id;
    $krankId = $categories->firstWhere('name', 'Krank')?->id;

    $userStats = [];

    foreach ($allUsers as $user) {
        $userTasks = collect($calendarData)->where('user', $user->name);

        $urlaubStage = $userTasks->filter(function($task) use ($urlaubId) {
            return in_array($urlaubId, array_column($task['categories'] ?? [], 'id'));
        });
 
        $montageStage = $userTasks->filter(function($task) use ($montageId) {
            return in_array($montageId, array_column($task['categories'] ?? [], 'id'));
        });

        $krankStage = $userTasks->filter(function($task) use ($krankId) {
            return in_array($krankId, array_column($task['categories'] ?? [], 'id'));
        });

        // Urlaubstage korrekt zählen, ohne Feiertage

    $urlaubStageDays = 0;
$isFourDayWeek = $user->four_day_week;

foreach ($urlaubStage as $task) {
    $start = Carbon::parse($task['start'])->startOfDay();
    $end = isset($task['end_time']) ? Carbon::parse($task['end_time'])->endOfDay() : $start->copy()->endOfDay();

    while ($start->lte($end)) {
        if ($start->isWeekday()) {
            $holidays = getPublicHolidays($start->year);
            $isHoliday = collect($holidays)->contains(fn ($holiday) => $holiday->isSameDay($start));
 
            $isWorkdayForUser = !$isFourDayWeek || ($start->dayOfWeek >= Carbon::MONDAY && $start->dayOfWeek <= Carbon::THURSDAY);

            if (!$isHoliday && $isWorkdayForUser) {
                $urlaubStageDays++;
            }
        }
        $start->addDay();
    }
}



        $krankStageDays = 0;
foreach ($krankStage as $task) {
    $start = Carbon::parse($task['start'])->startOfDay();
    $end = isset($task['end_time']) ? Carbon::parse($task['end_time'])->endOfDay() : $start->copy()->endOfDay();

    while ($start->lte($end)) {
        if ($start->isWeekday()) {
            $holidays = getPublicHolidays($start->year);
            $isHoliday = collect($holidays)->contains(function ($holiday) use ($start) {
                return $holiday->isSameDay($start);
            });

            if (!$isHoliday) {
                $krankStageDays++;
            }
        }
        $start->addDay();
    }
}


    $montageStageDays = 0;
foreach ($montageStage as $task) {
    $start = Carbon::parse($task['start'])->startOfDay();
    $end = isset($task['end_time']) ? Carbon::parse($task['end_time'])->endOfDay() : $start->copy()->endOfDay();

    while ($start->lte($end)) {
        if ($start->isWeekday()) {
            $holidays = getPublicHolidays($start->year);
            $isHoliday = collect($holidays)->contains(function ($holiday) use ($start) {
                return $holiday->isSameDay($start);
            });

            if (!$isHoliday) {
                $montageStageDays++;
            }
        }
        $start->addDay();
    }
}

            $userStats[$user->name] = [
            'urlaub_genommen' => $urlaubStageDays,
            'urlaub_verfuegbar' => $user->vacation_days ?? 0,
            'montage' => $montageStageDays,
            'krank' => $krankStageDays,
        ];

    }
@endphp




@extends('layouts.app')

@section('title', 'Auftragskalender')

@section('content')

<script>
function toggleSpecialColumns() {
    const specialCols = document.querySelectorAll('.special-column');
    const cardBody = document.querySelector('.card-body'); // Laravel Standard-Klasse

    let showing = specialCols[0]?.style.display !== 'none';

    specialCols.forEach(col => {
        col.style.display = showing ? 'none' : '';
    });

    if (!showing) {
        cardBody.classList.add('wide-mode');
    } else {
        cardBody.classList.remove('wide-mode');
    }
}
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">



<h1>Auftragskalender</h1>

<!-- Navigation & Filter Card -->
<div class="container my-4">
    <div class="border-0">
        <div class="card-body">
            <div class="row g-3 align-items-center justify-content-between">

                <!-- Monat/Jahr Auswahl -->
                <div class="col-md-auto d-flex align-items-center gap-2 flex-wrap">
                    <label class="form-label mb-0">Zeitraum:</label>

                    <select id="yearSelect" class="form-select form-select-sm" style="width: 100px;">
                        @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                            <option value="{{ $y }}" {{ $y == \Carbon\Carbon::parse($selectedDate)->year ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>

                    <select id="monthSelect" class="form-select form-select-sm" style="width: 130px;">
                        @for ($m = 1; $m <= 12; $m++)
                            @php $monthName = \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F'); @endphp
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $m == \Carbon\Carbon::parse($selectedDate)->month ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endfor
                    </select>

                    <button type="submit" class="btn btn-success btn-sm" form="jumpForm">Springen</button>
                </div>

                <!-- Monatsnavigation -->
                <div class="col-md-auto d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('tasks.planner', ['date' => \Carbon\Carbon::parse($selectedDate)->subMonth()->format('Y-m')]) }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-chevron-left me-1"></i> Vorheriger Monat
                    </a>

                    <a href="{{ route('tasks.planner', ['date' => \Carbon\Carbon::parse($selectedDate)->addMonth()->format('Y-m')]) }}"
                       class="btn btn-outline-primary btn-sm">
                        Nächster Monat <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>

                <!-- Admin Tools -->
                @if (auth()->user() && auth()->user()->isAdmin())
                <div class="col-md-auto d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-outline-info btn-sm" onclick="toggleSpecialColumns()">
                        <i class="fas fa-columns me-1"></i> Spalten
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="toggleSidebar()">
                        <i class="fas fa-tasks me-1"></i> Offene Aufträge
                    </button>
                </div>
                @endif

            </div>

            <hr>

            <!-- Weitere Monate & Filter -->
            <div class="row g-3 align-items-center justify-content-between">

                <div class="col-md-auto d-flex align-items-center gap-2">
                    <label for="monthCount" class="form-label mb-0">Weitere Monate:</label>
                    <select id="monthCount" class="form-select form-select-sm" style="width: 80px;">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <button class="btn btn-secondary btn-sm" id="loadSelectedMonths">Anzeigen</button>
                </div>

            @if(auth()->user()?->isAdmin())

                <div class="col-md-auto d-flex align-items-center gap-2">
                    <label for="roleFilter" class="form-label mb-0">Benutzer filtern:</label>
                    <select id="roleFilter" class="form-select form-select-sm" style="width: 150px;">
                        <option value="all">Alle</option>
                        <option value="admin">Admins</option>
                        <option value="employee">Mitarbeiter</option>
                    </select>
                </div>
            @endif

            </div>
        </div>
    </div>
</div>

<!-- Unsichtbares Form für den "Springen"-Button -->
<form id="jumpForm"></form>





<h2>Auftragsübersicht für {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('F Y') }}</h2>
<p>@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif</p>







<div id="task-sidebar">
    <h4>Offene Aufträge</h4>
    <div id="unassigned-tasks">
        <!-- Wird per JS geladen -->
        <p>Lade...</p>
    </div>
</div>





<!-- Calendar layout -->
<div id="custom-calendar" class="table-responsive">
    <table class="table table-bordered planner-table">
        <!-- Dates row -->
        <thead>
            <tr>
                <th class="special-column" style="width: 70px; display:none;"><i class="fas fa-plane"></i></th>
                <th class="special-column" style="width: 70px; display:none;"><i class="fas fa-tools"></i></th>
                <th class="special-column" style="width: 70px; display:none;"><i class="fas fa-briefcase-medical"></i></th>

                <th style="width:130px"></th> 
                @foreach(range(1, Carbon::parse($selectedDate)->daysInMonth) as $day)
                    @php
                        $currentDate = Carbon::parse($selectedDate . '-' . $day);
                        $isToday = $currentDate->isToday();
                        $isHoliday = in_array($currentDate->startOfDay(), getPublicHolidays($currentDate->year));
                    @endphp
                    <th class="day-header {{ $isToday ? 'today-highlight' : '' }} {{ $isHoliday ? 'holiday-column' : '' }}">
                        {{ $day }}
                        <br>
                        {{ $currentDate->locale('de')->isoFormat('dd') }}
                    </th>
                @endforeach

            </tr>
        </thead>
        
        <tbody>
            @php
                $users = collect($calendarData)->groupBy('user');
            @endphp

            @foreach($users as $user => $tasks)
                @php
                    $userModel = \App\Models\User::where('name', $user)->first();
                    $userRole = $userModel?->role ?? 'employee';
                @endphp

                @if(auth()->user()->role === 'admin' || auth()->user()->name === $user)
                    <tr class="user-row" data-role="{{ $userRole }}">
            <!-- Sonder-Spalten Urlaub, Montage, Krank -->
            <td class="special-column" style="display:none;">
                {{ $userStats[$user]['urlaub_genommen'] ?? 0 }} / {{ $userStats[$user]['urlaub_verfuegbar'] ?? 0 }}
            </td>
            <td class="special-column" style="display:none;">
                {{ $userStats[$user]['montage'] ?? 0 }}
            </td>
            <td class="special-column" style="display:none;">
                {{ $userStats[$user]['krank'] ?? 0 }}
            </td>

            <td class="user-column">{{ $user }}</td>



            @foreach(range(1, Carbon::parse($selectedDate)->daysInMonth) as $day)
                @php
                    $currentDate = Carbon::parse($selectedDate . '-' . $day);
                    $isWeekend = in_array($currentDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);
                    $isToday = $currentDate->isToday();


                    $tasksForDay = $tasks->filter(function($task) use ($currentDate) {
                    $start = Carbon::parse($task['start'])->startOfDay();
                    $end = isset($task['end_time']) ? Carbon::parse($task['end_time'])->endOfDay() : $start->copy()->endOfDay();
                    $dateOnly = $currentDate->copy()->startOfDay();

                    if ($dateOnly->isWeekend()) {
                        return false;
                    }

                    return $dateOnly->between($start, $end);

});


                    
                    $firstTask = $tasksForDay->first();
                    $taskColor = $firstTask['color_picker'] ?? '#ffffff';
                @endphp


                @php
    $isHoliday = in_array($day, $publicHolidayDays);
@endphp
                
    @php
        $userModel = \App\Models\User::where('name', $user)->first();
        $isFourDayUser = $userModel?->four_day_week === 1;
        $isFriday = $currentDate->dayOfWeek === \Carbon\Carbon::FRIDAY;
    @endphp

<td class="task-cell
    {{ $isWeekend ? 'weekend' : '' }}
    {{ $isToday ? 'today-highlight' : '' }}
    {{ $isHoliday ? 'holiday-column' : '' }}
    {{ $isFourDayUser && $isFriday ? 'four-day-friday' : '' }}"
    style="background-color: {{ $taskColor }};"
    data-user="{{ $user }}"
    data-date="{{ $currentDate->format('Y-m-d') }}"
    ondragover="event.preventDefault()"
    ondrop="handleDrop(event)">

                    @if($tasksForDay->isEmpty())
                        @if(auth()->user()?->isAdmin())
                            <div class="no-task" data-bs-toggle="modal" data-bs-target="#createTaskModal2">
                                <button class="createnewmodule" data-name="{{ $user }}" data-date="{{ $currentDate }}"></button>
                            </div>
                        @endif
                    @else
                        @foreach($tasksForDay as $task)
                            @php
                                $isFriday = $currentDate->dayOfWeek === \Carbon\Carbon::FRIDAY;
                                $userModel = \App\Models\User::where('name', $user)->first();
                                $isFourDayUser = $userModel?->four_day_week === 1;
                            @endphp

                            @if(!empty($task['icon']) && !($isFourDayUser && $isFriday))
                                <div class="task-icon view-task" data-task="{{ json_encode($task) }}"
                                     data-bs-toggle="modal" data-bs-target="#taskModal"
                                     draggable="true" data-task-id="{{ $task['id'] }}"
                                     ondragstart="handleDragStart(event)">
                                    <img src="{{ asset('storage/' . $task['icon']) }}" alt="Task Icon" style="width: 20px; height: 20px;">
                                </div>
                            @endif
                        @endforeach
                    @endif
                </td>
            @endforeach
        </tr>
    @endif
@endforeach







        </tbody>
    </table>
</div>


<!-- Hier wird der nächste Monat eingefügt -->
<div id="next-month-placeholder" style="display: none;"></div>


@if(auth()->user()?->isAdmin())
<div class="modal fade" id="createTaskModal2" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-body" style="padding: 4%" id="modalBodyContent">

                <!-- Form -->
                <form id="taskForm" >
                    @csrf

                <!-- Title, Date, and Categories -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5 class="modal-title fs-3" id="createTaskModalLabel">Neuen Auftrag erstellen</h5>
                        <p id="currentDate" class="text-muted mt-1 fs-5"></p>

                        <div class="mb-3">
                            <label for="title" class="form-label">Bezeichnung des Auftrags</label>
                            <input type="text" name="title" id="title" class="form-control custom-width" required>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="categories" class="form-label">Art des Auftrags</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" id="category_{{ $category->id }}" value="{{ $category->id }}">
                                    <label class="form-check-label" for="category_{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>


                <!-- Two Columns Layout -->
                <div class="row mt-5">
                    <!-- Left Side: Title & User ID -->
                    <div class="col-md-4">



                        <div class="mb-3">
                            <label for="user_id" class="form-label">Zuständiger Mitarbeiter</label>
                            <select name="user_id" id="user_id" class="form-control ">

                                <option value="">Mitarbeiter auswählen</option>
    
                                @foreach ($myUsers as $myUser)
                                
                                    <option value="{{ $myUser->id }}" {{ old('user_id') == $myUser->id ? 'selected' : '' }}>
                                        {{ $myUser->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('user_id'))
                                <div class="text-danger">{{ $errors->first('user_id') }}</div>
                            @endif
                        </div>

                    </div>

                    <!-- Right Side: Sonstiges (Extra Info) -->
                    <div class="col-md-8">
                        <div class="mb-3 ps-4" style="margin-left: 150px;">
                            <label class="form-label">Sonstiges</label>
                            <div class="p-2 rounded">
                                <p class="mb-1">Mitarbeiter: <span class="fw-bold">{{ Auth::user()->name }}</span></p>
                                <p class="mb-1">Auftrag angelegt am: <span class="text-muted">{{ date('d-m-Y') }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer, Status & Color Picker -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="customer_id" class="form-label">Kunde</label>
                        <select name="customer_id" id="customer_id" class="form-control searchable-dropdown" required>
                            <option value="">Kunde auswählen</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Bitte Status wählen</option>
                            <option value="open">Offen</option>
                            <option value="done">Erledigt</option>
                            <option value="not_done">Nicht erledigt</option>
                            <option value="billed">Abgerechnet</option>
                        </select>
                    </div>
                    
                </div>

                <!-- Startzeit & Endzeit -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_time" class="form-label">Startdatum</label>
                        <input type="date" name="start_time" id="start_time" class="form-control"
                            value="{{ old('start_time') ? \Carbon\Carbon::parse(old('start_time'))->format('Y-m-d') : '' }}" >
                        @if ($errors->has('start_time'))
                            <div class="text-danger">{{ $errors->first('start_time') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="end_time" class="form-label">Enddatum</label>
                        <input type="date" name="end_time" id="end_time" class="form-control"
                            value="{{ old('end_time') }}" >
                        @if ($errors->has('end_time'))
                            <div class="text-danger">{{ $errors->first('end_time') }}</div>
                        @endif
                    </div>                    
                </div>

                <!-- Beschreibung -->
                <div class="mb-5">
                    <label for="comment" class="form-label">Beschreibung</label>
                    <textarea class="form-control" id="comment" name="comment" rows="4" style="width: 70%;">{{ old('description') }}</textarea>
                    @if ($errors->has('comment'))
                        <div class="text-danger">{{ $errors->first('description') }}</div>
                    @endif
                </div>

                <!-- Speichern -->
                <button type="submit" class="btn btn-success pr-1">Speichern</button>
                <button type="submit" class="btn btn-primary">Servicebericht Wissner erstellen</button>

                </form>
            </div>
        </div>
    </div>
</div>
@endif




<!-- -CREATE MODAL  -->

<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Neuen Auftrag erstellen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <!-- Form will be dynamically loaded here -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Laden...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- show modal -->

<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body" id="modalBody">
                <!-- The content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- End CREATE MODAL -->
<!-- Task Edit  Modal -->
<!-- Edit Button -->
<!-- Edit Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Auftrag bearbeiten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Edit form content will be loaded here dynamically -->
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button> -->
            </div>
        </div>
    </div>
</div>


@endsection

@section('styles')
@endsection
<style>

    #custom-calendar {
    transition: all 0.3s ease;
}

.card-body.wide-mode {
    max-width: 92% !important;
    width: 92% !important;
    overflow-x: auto;
}

    .today-header {
    background-color: #ffdddd !important;
    color: #b30000;
    font-weight: bold;
    border: 2px solid #ff0000;
}

    .today-highlight {
    background-color: #c6e0f5 !important; /* hellblau wie bei MS Outlook */
    font-weight: bold;
}

.today-highlight::after {
    background-color: #000;
    position: absolute;
    top: 2px;
    right: 4px;
    font-size: 0.7em;
    color: #ff0000;
    font-weight: bold;
}


    .custom-width {
        max-width: 332px;
    }

    .createnewmodule {
    background: none;
    border: none;
    width: 20px;
    height: 20px;
    display: inline-block;
    }

    /* General table styling */
    .planner-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Ensures all cells are equal width */
    }

    .planner-table th,
    .planner-table td {
        border: 2px solid #333; /* Adjusted border style */
        text-align: center;
        vertical-align: middle;
        padding: 5px; /* Adjust padding for better spacing */
    }

    .day-header {
        background-color: #f5f5f5;
        font-weight: normal;
        font-size: 0.8em;
    }

    .user-column {
    
        font-weight: normal;
        text-align: left !important;
        width: 200px;
        font-size:15px;
    }

    .task-cell {
        height: 30px; /* Fixed height for uniformity */
        position: relative;
    }

    .task-icon {
        font-size: 1.2em;
        margin: auto;
        display: inline-block;
        cursor: pointer;
    }

    /* Highlight weekends */
    .planner-table td:nth-child(7),
    .planner-table td:nth-child(8) {
 
    }

    /* Today highlight */
    .planner-table td[data-highlight="today"] {
        background-color: #ccffcc; /* Light green for today */
    }
    .weekend {
    background-color: #f9ea5e !important; /* Light yellow for weekends */
}
.table-bordered>:not(caption)>*:first-child {
    border-width: 0px;
}
.table-bordered>:not(caption)>*>*:first-child {
    border-width: 0px !important;
}
.table>:not(:first-child) {
    border-top: 1px solid currentColor !important;
}
textarea {
    min-height: 150px !important; /* Forcefully increase height */
    height: auto !important; /* Prevent fixed height */
}
</style>


@section('scripts')
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleFilter = document.getElementById('roleFilter');
    
    function getAllUserRows() {
    return document.querySelectorAll('.user-row');
}

    function rebindTaskIcons() {
        document.querySelectorAll('.view-task').forEach(icon => {
            icon.removeEventListener('click', handleViewTaskClick); // doppelte vermeiden
            icon.addEventListener('click', handleViewTaskClick);
        });
    }

    function handleViewTaskClick(event) {
        const taskData = JSON.parse(this.getAttribute('data-task'));
        const taskId = taskData.id;

        $('#modalBody').html('<p>Loading...</p>');

        $.ajax({
            url: `/tasks/${taskId}`,
            method: 'GET',
            success: function (response) {
                $('#modalBody').html(response);
            },
            error: function () {
                $('#modalBody').html('<p>Fehler beim Laden des Auftrags.</p>');
            }
        });
    }

    // Beim Filtern anzeigen/verstecken
    roleFilter.addEventListener('change', function () {
        const selectedRole = this.value;

        getAllUserRows().forEach(row => {
            const rowRole = row.dataset.role;
            const show = selectedRole === 'all' || selectedRole === rowRole;
            row.style.display = show ? '' : 'none';
        });

        // Events neu binden
        rebindTaskIcons();
    });

    // Beim ersten Laden initial binden
    rebindTaskIcons();
});
</script>





<script>
$(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const createTaskButton = document.getElementById('createTaskButton');
        const modalBodyContent = document.getElementById('modalBodyContent');

        createTaskButton.addEventListener('click', function () {
            const route = this.getAttribute('data-route');
            modalBodyContent.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Laden...</span>
                    </div>
                </div>
            `;
            fetch(route)
                .then(response => response.text())
                .then(data => {
                    modalBodyContent.innerHTML = data;
                })
                .catch(error => {
                    modalBodyContent.innerHTML = `<p class="text-danger">Fehler beim Laden des Formulars.</p>`;
                    console.error('Error loading form:', error);
                });
        });
    });

    $(document).ready(function () {
        $('#taskForm').submit(function (event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('tasks.store') }}",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Auftrag erfolgreich erstellt!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    $('#createTaskModal2').modal('hide');
                    $('#taskForm')[0].reset();
                    setTimeout(() => location.reload(), 2000);
                },
                error: function (xhr) {
                    let errorMessage = 'Ein unbekannter Fehler ist aufgetreten.';
                    if (xhr.responseJSON?.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Fehler', text: errorMessage });
                }
            });
        });
    });
});
</script>




<script>
$(document).ready(function () {
    $('#createTaskModal2').on('shown.bs.modal', function () {
        function toggleFields() {
            const selectedCategories = Array.from(document.querySelectorAll('#createTaskModal2 input[name="categories[]"]:checked')).map(input => parseInt(input.value));
            const urlaubId = @json($urlaubId);
            const krankId = @json($krankId);

            const isVacationOrSick = selectedCategories.includes(urlaubId) || selectedCategories.includes(krankId);

            const titleField = document.getElementById('title')?.closest('.mb-3');
            const customerField = document.getElementById('customer_id')?.closest('.col-md-4');
            const statusField = document.getElementById('status')?.closest('.col-md-4');

            [titleField, customerField, statusField].forEach(field => {
                if (!field) return;
                field.style.display = isVacationOrSick ? 'none' : '';
            });

            ['title', 'customer_id', 'status'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                isVacationOrSick ? el.removeAttribute('required') : el.setAttribute('required', 'required');
            });
        }

        toggleFields();
        $('#createTaskModal2 input[name="categories[]"]').off('change').on('change', toggleFields);
    });
});
</script>


<script>
$(document).on('click', '.createnewmodule', function () {
    $('#user_id').val($(this).data('name'));
    $('#modalInputDate').val($(this).data('date'));
});

$(document).on('click', '.view-task', function () {
    const taskData = JSON.parse(this.getAttribute('data-task'));
    const taskId = taskData.id;
    $('#modalBody').html('<p>Loading...</p>');

    $.ajax({
        url: `/tasks/${taskId}`,
        method: 'GET',
        success: function (response) {
            $('#modalBody').html(response);
        },
        error: function () {
            $('#modalBody').html('<p>An error occurred while loading the task details.</p>');
        }
    });
});
</script>


<script>
$(document).on('click', '.open-edit-modal', function () {
    const taskId = $(this).data('id');
    $('#editTaskModal').modal('show');
    $('#modalContent').html('<p class="text-center">Lade...</p>');

    $.ajax({
        url: `/tasks/${taskId}/edit`,
        type: 'GET',
        success: function (response) {
            $('#modalContent').html(response);
        },
        error: function () {
            $('#modalContent').html('<p class="text-danger">Fehler beim Laden des Formulars.</p>');
        }
    });
});

$(document).on('submit', '#editTaskForm', function (e) {
    e.preventDefault();
    const $form = $(this);
    const formData = $form.serialize() + '&_method=PUT';

    $.ajax({
        url: $form.data('action'),
        type: 'POST',
        data: formData,
        success: function () {
            Swal.fire({ icon: 'success', title: 'Erfolgreich gespeichert!', timer: 2000, showConfirmButton: false });
            $('#editTaskModal').modal('hide');
            setTimeout(() => location.reload(), 2000);
        },
        error: function (xhr) {
            let msg = 'Fehler beim Speichern.';
            if (xhr.responseJSON?.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            }
            Swal.fire({ icon: 'error', title: 'Fehler', text: msg });
        }
    });
});
</script>


<script>
function toggleSidebar() {
    document.getElementById('task-sidebar').classList.toggle('open');
}
</script>


<script>
function loadUnassignedTasks() {
    fetch('/tasks/unassigned')
        .then(response => response.json())
        .then(tasks => {
            const container = document.getElementById('unassigned-tasks');
            container.innerHTML = '';

            if (tasks.length === 0) {
                container.innerHTML = '<p>Keine offenen Aufträge</p>';
                return;
            }

            const groupedTasks = {};
            tasks.forEach(task => {
                const category = (task.categories && task.categories.length > 0)
                    ? task.categories[0].name
                    : 'Keine Kategorie';

                if (!groupedTasks[category]) {
                    groupedTasks[category] = [];
                }
                groupedTasks[category].push(task);
            });

            const sortedCategories = Object.keys(groupedTasks).sort();
            sortedCategories.forEach(categoryName => {
                const header = document.createElement('h5');
                header.textContent = categoryName;
                header.style.marginTop = '15px';
                header.style.borderBottom = '1px solid #ccc';
                header.style.paddingBottom = '4px';
                container.appendChild(header);

                groupedTasks[categoryName].forEach(task => {
                    const div = document.createElement('div');
                    div.className = 'draggable-task';
                    div.textContent = task.title;
                    div.setAttribute('draggable', 'true');
                    div.dataset.taskId = task.id;

                    if (task.categories && task.categories.length > 0) {
                        const catLabel = document.createElement('small');
                        catLabel.textContent = ` (${task.categories[0].name})`;
                        catLabel.style.color = '#666';
                        div.appendChild(catLabel);
                    }

                    container.appendChild(div);
                });
            });
        });
}
</script>

<script>
document.addEventListener('dragstart', function (e) {
    if (e.target.classList.contains('draggable-task')) {
        e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    loadUnassignedTasks();
});
</script>



<script>
function handleDrop(e) {
    e.preventDefault();

    const taskId = e.dataTransfer.getData('text/plain');
    const user = e.currentTarget.dataset.user;
    const date = e.currentTarget.dataset.date;

    if (!taskId || !user || !date) {
        alert("Ungültiger Drop.");
        return;
    }

    fetch(`/tasks/${taskId}/assign`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_name: user, date: date })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Zugewiesen!',
                showConfirmButton: false,
                timer: 1200
            });
            loadUnassignedTasks();
            setTimeout(() => location.reload(), 1000);
        } else {
            throw new Error(data.message || 'Fehler bei der Zuweisung.');
        }
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Fehler', text: err.message });
    });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('jumpForm');
    const yearSelect = document.getElementById('yearSelect');
    const monthSelect = document.getElementById('monthSelect');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const newDate = `${yearSelect.value}-${monthSelect.value}`;
        window.location.href = `/planner?date=${newDate}`;
    });
});
</script>


<script>
function handleDragStart(event) {
    let taskId = event.target.dataset.taskId 
        || event.target.closest('[data-task-id]')?.dataset.taskId;

    if (!taskId) {
        alert('❌ Kein gültiger Task ID gefunden!');
        return;
    }

    event.dataTransfer.setData('text/plain', taskId);
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loadButton = document.getElementById('loadSelectedMonths');
    const monthSelect = document.getElementById('monthCount');
    const placeholder = document.getElementById('next-month-placeholder');
    let currentDate = "{{ \Carbon\Carbon::parse($selectedDate)->format('Y-m') }}";

    loadButton.addEventListener('click', function () {
        const monthsToLoad = parseInt(monthSelect.value);
        let [year, month] = currentDate.split('-').map(Number);
        placeholder.innerHTML = ''; // Leeren

        const fetchPromises = [];

        for (let i = 0; i < monthsToLoad; i++) {
            let nextDate = new Date(year, month - 1 + i + 1, 1);
            let y = nextDate.getFullYear();
            let m = String(nextDate.getMonth() + 1).padStart(2, '0');
            let nextParam = `${y}-${m}`;

            const promise = fetch(`/planner?date=${nextParam}&withNext=true`)
                .then(response => response.text())
                .then(html => {
                    return {
                        date: nextParam,
                        html: html
                    };
                });

            fetchPromises.push(promise);
        }

        Promise.all(fetchPromises)
            .then(results => {
                // Nach Datum sortieren
                results.sort((a, b) => new Date(a.date) - new Date(b.date));

                results.forEach(result => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(result.html, 'text/html');
                    const newCalendar = doc.querySelector('#custom-calendar')?.outerHTML || '';
                    const newTitle = doc.querySelector('h2')?.outerHTML || '';
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = newTitle + newCalendar;
                    placeholder.appendChild(wrapper);
                });
                 // Spalten Einblendung weitere Monate
                        const specialColsVisible = document.querySelector('.special-column')?.style.display !== 'none';
                        document.querySelectorAll('#next-month-placeholder .special-column').forEach(col => {
                            col.style.display = specialColsVisible ? '' : 'none';
                        });

                        placeholder.style.display = 'block';
                    })
            .catch(err => {
                console.error('Fehler beim Laden der Monate:', err);
                Swal.fire('Fehler', 'Ein oder mehrere Monate konnten nicht geladen werden.', 'error');
            });
    });
});
</script>


</script>


<style type="text/css">
    
    #task-sidebar {
    position: fixed;
    left: 0;
    top: 60px;
    width: 250px;
    background: #ffffff;
    padding: 10px;
    height: 100%;
    overflow-y: auto;
    transition: transform 0.3s ease;
    transform: translateX(-100%);
    box-shadow: 0px 0px 11px 0px #a9a9a9;
    border-bottom-right-radius: 15px;
    border-top-right-radius: 15px;
}

#task-sidebar.open {
    transform: translateX(0);
}

.draggable-task {
    background: #fff;
    border: 1px solid #aaa;
    margin-bottom: 5px;
    padding: 6px;
    cursor: move;
}

#unassigned-tasks h5 {
    border-bottom: 1px solid #ccc;
    margin-bottom: 5px;
    padding-bottom: 2px;
}



.holiday {
    background-color: #d4f4d7 !important; /* sanftes Grün */
    color: #107c10 !important;
    font-weight: bold;
    border: 2px solid #8bc34a;
}


.holiday-column {
    background-color: #d4f4d7 !important; /* zartes grün */
    color: #107c10 !important;
    font-weight: bold;
}


.four-day-friday {
    background-color: #9b9b9b !important;
    opacity: 0.8;
}




</style>

