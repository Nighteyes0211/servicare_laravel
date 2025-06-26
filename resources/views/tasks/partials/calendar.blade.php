<h2 id="next-month-title" class="mt-5">
    Folgender Monat: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('F Y') }}
</h2>

<div class="calendar-month table-responsive mt-4">
    <table class="table table-bordered planner-table">
        <thead>
            <tr>
                <th class="special-column" style="width: 70px; display:none;"><i class="fas fa-plane"></i></th>
                <th class="special-column" style="width: 70px; display:none;"><i class="fas fa-tools"></i></th>
                <th class="special-column" style="width: 70px; display:none;"><i class="fas fa-briefcase-medical"></i></th>
                <th style="width:130px"></th>
                @foreach(range(1, \Carbon\Carbon::parse($selectedDate)->daysInMonth) as $day)
                    @php
                        $currentDate = \Carbon\Carbon::parse($selectedDate . '-' . $day);
                        $isToday = $currentDate->isToday();
                    @endphp
                    <th class="day-header {{ $isToday ? 'today-highlight' : '' }}">
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
                <tr>
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

                    @foreach(range(1, \Carbon\Carbon::parse($selectedDate)->daysInMonth) as $day)
                        @php
                            $currentDate = \Carbon\Carbon::parse($selectedDate . '-' . $day);
                            $isWeekend = in_array($currentDate->dayOfWeek, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY]);
                            $isToday = $currentDate->isToday();

                            $tasksForDay = $tasks->filter(function($task) use ($currentDate) {
                                $start = \Carbon\Carbon::parse($task['start'])->startOfDay();
                                $end = isset($task['end_time']) ? \Carbon\Carbon::parse($task['end_time'])->endOfDay() : $start->copy()->endOfDay();
                                $dateOnly = $currentDate->copy()->startOfDay();
                                return !$dateOnly->isWeekend() && $dateOnly->between($start, $end);
                            });

                            $firstTask = $tasksForDay->first();
                            $taskColor = $firstTask['color_picker'] ?? '#ffffff';
                        @endphp

                        <td class="task-cell {{ $isWeekend ? 'weekend' : '' }} {{ $isToday ? 'today-highlight' : '' }}" style="background-color: {{ $taskColor }};">
                            @if($tasksForDay->isEmpty())
                                <div class="no-task" data-bs-toggle="modal" data-bs-target="#createTaskModal2">
                                    <button class="createnewmodule" data-name="{{ $user }}" data-date="{{ $currentDate }}"></button>
                                </div>
                            @else
                                @foreach($tasksForDay as $task)
                                    @if(!empty($task['icon']))
                                        <div class="task-icon view-task" data-task="{{ json_encode($task) }}" data-bs-toggle="modal" data-bs-target="#taskModal">
                                            <img src="{{ asset('storage/' . $task['icon']) }}" alt="Task Icon" style="width: 20px; height: 20px;">
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
