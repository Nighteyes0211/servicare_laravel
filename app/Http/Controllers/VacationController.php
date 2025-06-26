<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeVacation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\Task;
use App\Models\TaskCategory;
use Carbon\Carbon;

class VacationController extends Controller
{
    public function index() {

        if(auth()->user()->isAdmin()) {
            $vacations = EmployeeVacation::orderBy('id', 'desc')->get();
        } else {
            $vacations = EmployeeVacation::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        }
        return view('vacations.index',compact('vacations'));
       }


    public function create(){
        return view('vacations.create');
    }

    public function store(Request $request)
    {
        // Validate the request inputs
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        $user = auth()->user(); // Get the authenticated user

        // Get the user's available vacation days
        $availableVacationDays = $user->vacation_days;

        // Calculate the total number of vacation days requested excluding Saturdays and Sundays
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $requestedDays = $startDate->diffInDaysFiltered(function ($date) {
            return !$date->isWeekend(); // Exclude weekends (Saturdays and Sundays)
        }, $endDate) + 1; // Include the start date if it's not a weekend

        // Check if the requested days exceed the user's available vacation days
        if ($requestedDays > $availableVacationDays) {
            return back()->withErrors(['error' => 'You do not have enough vacation days available.']);
        }

        // Store the data in the EmployeeVacation table
        EmployeeVacation::create([
            'user_id' => $user->id,
            'vacation_start_date' => $validated['start_date'],
            'vacation_end_date' => $validated['end_date'],
            'number_of_vacations' => $requestedDays,
            'vacation_status' => 'Pending',
            'apply_date' => date('Y-m-d'),
            'description' => $request->description,
        ]);


        // Decrease user's remaining vacation days
        // $user->vacation_days -= $requestedDays;
        $user->save();

        return redirect()->route('vacation.index')->with('success', 'Vacation request submitted successfully.');
    }

    public function show($id){

        $vacation = EmployeeVacation::find($id);
        return view('vacations.show',compact('vacation'));
    }

    public function showAdmin($id){

        $vacation = EmployeeVacation::find($id);
        $user_vacation = EmployeeVacation::where('user_id', $vacation->user_id )->get();
        return view('vacations.showAdmin',compact('vacation', 'user_vacation'));
    }

    public function showAllVacationAdmin($id){
        $user = User::find($id);
        $user_vacation = EmployeeVacation::where('user_id', $id )->get();
        return view('user_management.showUserVacation',compact('user_vacation', 'user'));
    }

    public function edit($id){
        $vacation = EmployeeVacation::find($id);
        return view('vacations.edit',compact('vacation'));
    }

    public function update(Request $request){
         // Validate the request inputs
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $user = auth()->user(); // Get the authenticated user
        $id = $request->leave_id;
        // Calculate vacation days excluding weekends
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $requestedDays = $startDate->diffInDaysFiltered(function ($date) {
            return !$date->isWeekend(); // Exclude Saturdays and Sundays
        }, $endDate) + 1; // Include the start date if it's not a weekend

        // Check if the requested days exceed the user's available vacation days
        if ($requestedDays > $user->vacation_days) {
            return back()->withErrors(['error' => 'You do not have enough vacation days available.']);
        }

        // Update the vacation record
        $vacation = EmployeeVacation::findOrFail($id);
        $vacation->update([
            'vacation_start_date' => $validated['start_date'],
            'vacation_end_date' => $validated['end_date'],
            'number_of_vacations' => $requestedDays,
            'vacation_status' => 'Pending',
            'description' => $request->description,
        ]);

        // Notify admins about the update
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Mail::raw(
                "Vacation request updated by user: {$user->name}, Start Date: {$validated['start_date']}, End Date: {$validated['end_date']}, Total Days: {$requestedDays}.",
                function ($message) use ($admin) {
                    $message->to($admin->email)
                        ->subject('Vacation Request Updated');
                }
            );
        }

        return redirect()->route('vacation.index')->with('success', 'Urlaubsantrag erfolgreich aktualisiert.');
        
    }

    public function actionUpdate(Request $request)
{

    $vacation = EmployeeVacation::findOrFail($request->vacation_id);
    $wasApprovedBefore = $vacation->vacation_status === 'approve';
    $vacation->vacation_status = $request->leave_action;
    $vacation->approved_by = auth()->user()->id;
    $vacation->save();

    $user = User::findOrFail($vacation->user_id);

    if ($request->leave_action === 'approve') {
        // Prüfe Urlaubstage
        if ($vacation->number_of_vacations > $user->vacation_days) {
            return back()->withErrors(['error' => 'Nicht genug Urlaubstage vorhanden.']);
        }

        // Urlaubstage abziehen
        $user->vacation_days -= $vacation->number_of_vacations;
        $user->save();

        // ✅ Prüfen ob bereits ein Task angelegt wurde
        if (!$vacation->task_id) {
            $urlaubCategory = \App\Models\TaskCategory::where('name', 'Urlaub')->first();
            if (!$urlaubCategory) {
                return back()->withErrors(['error' => 'Kategorie "Urlaub" nicht gefunden.']);
            }

            $task = new \App\Models\Task();
            $task->title = 'Urlaub - ' . $user->name;
            $task->user_id = $user->id;
            $task->start_time = \Carbon\Carbon::parse($vacation->vacation_start_date)->startOfDay();
            $task->end_time = \Carbon\Carbon::parse($vacation->vacation_end_date)->endOfDay();
            $task->status = 'done';
            $task->comment = $vacation->description ?? '';
            $task->save();

            $task->categories()->attach([$urlaubCategory->id]);

            $vacation->task_id = $task->id;
            $vacation->save();
        }

        // ✅ E-Mail an User
        Mail::raw(
            "Hallo {$user->name}, dein Urlaubsantrag vom {$vacation->vacation_start_date} bis {$vacation->vacation_end_date} (insg. {$vacation->number_of_vacations} Tage) wurde genehmigt.",
            function ($message) use ($user) {
                $message->to($user->email)->subject('Urlaub genehmigt');
            }
        );
    }

   if ($request->leave_action === 'reject') {
    // ⬅️ Wenn vorher genehmigt war → Urlaubstage zurückbuchen
    if ($wasApprovedBefore) {
        $user->vacation_days += $vacation->number_of_vacations;
        $user->save();
    }

    // Falls ein Task existiert – löschen
    if ($vacation->task_id) {
        $task = Task::find($vacation->task_id);
        if ($task) {
            $task->delete();
        }
        $vacation->task_id = null;
        $vacation->save();
    }

    // E-Mail senden
    Mail::raw(
        "Hallo {$user->name}, dein Urlaubsantrag vom {$vacation->vacation_start_date} bis {$vacation->vacation_end_date} wurde abgelehnt.",
        function ($message) use ($user) {
            $message->to($user->email)->subject('Urlaub abgelehnt');
        }
    );
}

return redirect()->route('vacation.index')->with('success', 'Urlaubsstatus aktualisiert.');
} // Ende der Methode actionUpdate()

    public function pendingApplicationCount(){
        $pendingApplicationCount = EmployeeVacation::where('vacation_status', 'Pending')->count();

        return response()->json([
            'total_count' => $pendingApplicationCount,
        ], 200);
    }
}
