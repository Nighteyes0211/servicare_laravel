<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Diagnosis;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\FirstServiceRecords;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Customer; // Hier wird die Customer-Klasse importiert
use Carbon\Carbon;  
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceRecord;




class TaskController extends Controller
{
    /**
     * Zeigt die Ansicht zum Erstellen einer Aufgabe.
     */
    public function create()
    {


        

        // $users = User::select('id', 'name')->get();
        $tasks = Task::select('id', 'title')->get();
        // $customers = Customer::select('id', 'company_name')->get();

        $categories = TaskCategory::all();
        $users = User::all();
        $customers = Customer::all();
        $diagnoses = Diagnosis::with('customer')->get();
        $articles = Article::all();
    
        if (request()->ajax()) {
            return view('tasks.create-form', compact('categories', 'tasks', 'users', 'customers', 'diagnoses', 'articles'));
        }
    
        return view('tasks.create', compact('categories', 'tasks', 'users', 'customers', 'diagnoses', 'articles'));
    }


    public function fetchArticles(Request $request){
        $search = $request->input('search');

        $articles = Article::where('article_number', 'LIKE', "%{$search}%")
            ->orWhere('description', 'LIKE', "%{$search}%")
            ->limit(10) 
            ->get(['id', 'article_number', 'description']);

        return response()->json($articles);
    }

    /**
     * Speichert eine neue Aufgabe in der Datenbank.
     */
    
    public function store(Request $request) {
    


    // Prüfen, ob Urlaub oder Krank ausgewählt wurde
    $isVacationOrSick = false;
    if ($request->has('categories')) {
        $selectedCategoryIds = $request->categories;
        $urlaubId = TaskCategory::where('name', 'Urlaub')->first()?->id;
        $krankId = TaskCategory::where('name', 'Krank')->first()?->id;

        if (in_array($urlaubId, $selectedCategoryIds) || in_array($krankId, $selectedCategoryIds)) {
            $isVacationOrSick = true;
        }
    }

    // Validation je nachdem ob Urlaub/Krank
    $rules = [
        'user_id' => 'nullable|exists:users,id',
        'start_time' => 'nullable|date',
        'end_time' => 'nullable|date|after_or_equal:start_time',
        'categories' => 'nullable|array',
        'categories.*' => 'exists:task_categories,id',
        'comment' => 'nullable|string',
    ];

    if (!$isVacationOrSick) {
        $rules['title'] = 'nullable|string|max:255';
        $rules['customer_id'] = 'required|exists:customers,id';
        $rules['status'] = 'nullable|in:open,done,not_done,billed';
        // priority wird ignoriert!
    }






    if ($request->ajax()) {
        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    } else {
        $validated = $request->validate($rules);
    }

if ($isVacationOrSick && (empty($validated['title']) || !isset($validated['title']))) {
    $firstCategoryId = $validated['categories'][0] ?? null;
    $categoryName = TaskCategory::find($firstCategoryId)?->name ?? '';
    
    if ($categoryName !== '') {
        $user = isset($validated['user_id']) ? User::findOrFail($validated['user_id']) : null;
        $validated['title'] = $categoryName . ' - ' . $user->name;
    }
}


    // Nutzer laden
    $user = isset($validated['user_id']) ? User::find($validated['user_id']) : null;
    $customer = isset($validated['customer_id']) ? Customer::findOrFail($validated['customer_id']) : null;


    // PDFs vorbereiten
    $pdfPath = null;
    $secondPdfPath = null;

    if (isset($request->service_report_id)) {
        $service = ServiceRecord::findOrFail($request->service_report_id);
        $service_material_consumption = DB::table('service_materials')->where('first_service_record_id', $request->service_report_id)->get();
        $service_travel_details = DB::table('first_service_travel_details')->where('first_service_record_id', $request->service_report_id)->get();

        $pdf = Pdf::loadView('tasks.pdf', [
            'modal_title' => $request->modal_title,
            'modal_description' => $request->modal_description,
            'user' => $user?->name ?? 'Unbekannt',
            'customer' => $customer?->company_name ?? '',
            'service' => $service,
            'service_travel_details' => $service_travel_details,
            'service_material_consumption' => $service_material_consumption,
        ]);

        $fileName = 'task_' . time() . '.pdf';
        $pdfPath = 'pdfs/' . $fileName;
        $pdf->save(public_path($pdfPath));
    }

    if (isset($request->second_service_report_id)) {
        $secondService = ServiceRecord::findOrFail($request->second_service_report_id);
        $service_material_consumption = DB::table('task_reports_material_consumption')->where('service_record_id', $request->second_service_report_id)->get();
        $service_travel_details = DB::table('service_travel_details')->where('service_record_id', $request->second_service_report_id)->get();

        $pdf2 = Pdf::loadView('tasks.pdf', [
            'modal_title' => $request->modal_title,
            'modal_description' => $request->modal_description,
            'user' => $user?->name ?? 'Unbekannt',
            'customer' => $customer?->company_name ?? '',
            'service' => $secondService,
            'service_travel_details' => $service_travel_details,
            'service_material_consumption' => $service_material_consumption,
        ]);

        $secondFileName = 'task_' . (time() + 1) . '.pdf'; // kleines Offset für unterschiedliche Zeitstempel
        $secondPdfPath = 'second_pdfs/' . $secondFileName;
        $pdf2->save(public_path($secondPdfPath));
    }

    // Task speichern

$task = new Task();
$task->contact_id = $request->input('contact_id');
$task->user_id = $validated['user_id'] ?? null;
$task->start_time = $validated['start_time'] ?? null;
$task->end_time = $validated['end_time'] ?? null;
$task->comment = $validated['comment'] ?? null;
$task->modal_title = $request->modal_title ?? '';
$task->modal_description = $request->modal_description ?? '';
$task->pdf_path = $pdfPath ?? '';
$task->second_pdf_path = $secondPdfPath ?? '';
$task->title = $validated['title'] ?? '';

if ($isVacationOrSick) {
    $task->status = 'done'; // 👈 Urlaub oder Krank = automatisch erledigt
} else {
    $task->customer_id = $validated['customer_id'];
    $task->status = $request->status; // Normaler Auftrag: Status aus Formular
    $task->order_number = $this->generateOrderNumber();
}

if (isset($request->service_report_id)) {
    $task->service_report_id = $request->service_report_id;
}
if (isset($request->second_service_report_id)) {
    $task->second_service_report_id = $request->second_service_report_id;
}

$task->save();

if (!$isVacationOrSick) {
    // Alte PDF ggf. löschen
    if ($task->pdf_path && file_exists(public_path($task->pdf_path))) {
        unlink(public_path($task->pdf_path));
    }

    // Neues PDF aus Auftragsdaten erstellen
    $taskWithRelations = Task::with(['customer', 'contact', 'user'])->find($task->id);
    $pdf = Pdf::loadView('tasks.pdf_auftrag', ['task' => $taskWithRelations]);

    $fileName = 'task_' . $task->id . '_' . time() . '.pdf';
    $pdfPath = 'pdfs/' . $fileName;
    $pdf->save(public_path($pdfPath));

    $task->pdf_path = $pdfPath;
    $task->save();
}



    // Kategorien verknüpfen
    if (!empty($validated['categories'])) {
        $task->categories()->sync($validated['categories']);
    }

    // PDFs in Zwischentabellen speichern (falls vorhanden)
    if ($pdfPath) {
        DB::table('table_task_service_files')->insert([
            'task_id' => $task->id,
            'first_services_id' => $request->service_report_id,
            'first_service_file' => $pdfPath,
        ]);
    }
    if ($secondPdfPath) {
        DB::table('table_task_second_service_files')->insert([
            'task_id' => $task->id,
            'service_id' => $request->second_service_report_id,
            'second_service_file' => $secondPdfPath,
        ]);
    }

    // Antwort
    if ($request->ajax()) {
        return response()->json([
            'message' => 'Task erfolgreich erstellt!',
            'task_id' => $task->id
        ]);
    }

    return redirect()->route('tasks.edit', $task->id)->with('success', 'Aufgabe erfolgreich erstellt.');
}



    public function index(Request $request)
{
    $user = auth()->user();

    // Basis-Query mit Relationen
   $urlaubCategory = TaskCategory::where('name', 'Urlaub')->first();

$query = Task::with(['user', 'categories', 'customer'])
    ->whereDoesntHave('categories', function ($q) use ($urlaubCategory) {
        $q->where('task_categories.id', $urlaubCategory?->id);
    });

    // Rollenbasiertes Filtern
    if ($user->role !== 'admin') {
        $query->where('user_id', $user->id);
    }

    // 🔍 Suche nach Titel, Auftrag-Nummer, Beschreibung, User, Kunde
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('order_number', 'like', "%{$search}%")
              ->orWhere('due_date', 'like', "%{$search}%")
              ->orWhere('comment', 'like', "%{$search}%")
              ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
              ->orWhereHas('customer', fn($q2) => $q2->where('company_name', 'like', "%{$search}%")
                                                    ->orWhere('city', 'like', "%{$search}%"));
        });
    }

    // 🟢 Status-Filter (offen, nicht erledigt, abgerechnet, erledigt)
    if ($request->filled('status')) {
        $query->where('status', $request->input('status'));
    }

    // Eingrenzen auf nur diese Status-Werte
    $query->whereIn('status', ['open', 'not_done', 'billed', 'done']);

    // 🔁 Sortierlogik basierend auf dem URL-Parameter "sort"
    $sort = $request->input('sort');
    switch ($sort) {
        case 'created_at_asc':
            $query->orderBy('created_at', 'asc');
            break;
        case 'created_at_desc':
            $query->orderBy('created_at', 'desc');
            break;
        case 'updated_at_asc':
            $query->orderBy('updated_at', 'asc');
            break;
        case 'updated_at_desc':
            $query->orderBy('updated_at', 'desc');
            break;
        default:
            $query->orderBy('id', 'desc'); // Fallback-Sortierung
            break;
    }

    // 📄 Pagination + Beibehaltung aller Query-Parameter außer "page"
    $tasks = $query
        ->paginate(10)
        ->appends($request->except('page'));

    return view('tasks.index', compact('tasks'));
}

    



   
    public function planner(Request $request)
{


    $selectedRole = $request->input('role', 'all');
    $selectedDate = $request->input('date', now()->format('Y-m'));

    $users = $selectedRole === 'all'
        ? User::all()
        : User::where('role', $selectedRole)->get();

    $dateInput = $request->input('date', now()->format('Y-m'));
    $withNext = $request->input('withNext', false);

    $user = auth()->user();

    $startOfMonth = Carbon::parse($dateInput)->startOfMonth();
    $endOfMonth = Carbon::parse($dateInput)->endOfMonth();

    $taskQuery = Task::with(['user', 'categories'])
        ->where(function ($query) use ($startOfMonth, $endOfMonth) {
            $query->where('start_time', '<=', $endOfMonth)
                  ->where(function ($q2) use ($startOfMonth) {
                      $q2->whereNull('end_time')->orWhere('end_time', '>=', $startOfMonth);
                  });
        });

    if ($user->role !== 'admin') {
        $taskQuery->where('user_id', $user->id);
    }

    $tasks = $taskQuery->get();

    $users = $user->role !== 'admin'
        ? User::where('id', $user->id)->get()
        : User::with('tasks.categories')->get();

    $calendarData = [];

    foreach ($users as $userItem) {
        $userTasks = $tasks->filter(fn($task) => $task->user_id === $userItem->id);

        foreach ($userTasks as $task) {
            $calendarData[] = [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->start_time,
                'end_time' => $task->end_time,
                'user' => $userItem->name,
                'description' => $task->description,
                'icon' => $task->categories->first()?->icon,
                'color_picker' => $task->color_picker ?? '#ffffff',
                'categories' => $task->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray(),
            ];
        }

        if ($userTasks->isEmpty()) {
            $calendarData[] = [
                'id' => null,
                'title' => '',
                'start' => null,
                'user' => $userItem->name,
                'description' => '',
                'icon' => null,
                'color_picker' => '#ffffff',
                'categories' => [],
            ];
        }
    }

    $nextMonthData = null;
    if ($withNext) {
        $nextDate = Carbon::parse($dateInput)->addMonth()->format('Y-m');
        $nextStart = Carbon::parse($nextDate)->startOfMonth();
        $nextEnd = Carbon::parse($nextDate)->endOfMonth();

        $nextTasks = Task::with(['user', 'categories'])
            ->where(function ($q) use ($nextStart, $nextEnd) {
                $q->where('start_time', '<=', $nextEnd)
                  ->where(function ($q2) use ($nextStart) {
                      $q2->whereNull('end_time')->orWhere('end_time', '>=', $nextStart);
                  });
            });

        if ($user->role !== 'admin') {
            $nextTasks->where('user_id', $user->id);
        }

        $nextCalendarData = [];
        foreach ($users as $userItem) {
            $userNextTasks = $nextTasks->get()->filter(fn($task) => $task->user_id === $userItem->id);

            foreach ($userNextTasks as $task) {
                $nextCalendarData[] = [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->start_time,
                    'end_time' => $task->end_time,
                    'user' => $userItem->name,
                    'description' => $task->description,
                    'icon' => $task->categories->first()?->icon,
                    'color_picker' => $task->color_picker ?? '#ffffff',
                    'categories' => $task->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray(),
                ];
            }

            if ($userNextTasks->isEmpty()) {
                $nextCalendarData[] = [
                    'id' => null,
                    'title' => '',
                    'start' => null,
                    'user' => $userItem->name,
                    'description' => '',
                    'icon' => null,
                    'color_picker' => '#ffffff',
                    'categories' => [],
                ];
            }
        }

        $nextMonthData = [
            'calendarData' => $nextCalendarData,
            'selectedDate' => $nextDate,
        ];
    }

    $myUsers = User::all();
    $allUsers = User::with('tasks.categories')->get();
    $customers = Customer::all();
    $categories = TaskCategory::all();
    $diagnoses = Diagnosis::with('customer')->get();

    Carbon::setLocale('de');

    if ($request->ajax() && $withNext && $nextMonthData) {
        $userStats = [];
        foreach ($allUsers as $userItem) {
            $userTasks = collect($nextMonthData['calendarData'])->where('user', $userItem->name);

            $urlaubId = $categories->firstWhere('name', 'Urlaub')?->id;
            $montageId = $categories->firstWhere('name', 'Montage')?->id;
            $krankId = $categories->firstWhere('name', 'Krank')?->id;

            $urlaubStage = $userTasks->filter(fn($task) => in_array($urlaubId, array_column($task['categories'] ?? [], 'id')));
            $montageStage = $userTasks->filter(fn($task) => in_array($montageId, array_column($task['categories'] ?? [], 'id')));
            $krankStage = $userTasks->filter(fn($task) => in_array($krankId, array_column($task['categories'] ?? [], 'id')));

            $urlaubStageDays = 0;
            foreach ($urlaubStage as $task) {
                $start = Carbon::parse($task['start'])->startOfDay();
                $end = isset($task['end_time']) ? Carbon::parse($task['end_time'])->endOfDay() : $start->copy()->endOfDay();
                while ($start->lte($end)) {
                    if ($start->isWeekday() && !collect(getPublicHolidays($start->year))->contains(fn($holiday) => $holiday->isSameDay($start))) {
                        $urlaubStageDays++;
                    }
                    $start->addDay();
                }
            }

            $krankStageDays = 0;
            foreach ($krankStage as $task) {
                $start = Carbon::parse($task['start'])->startOfDay();
                $end = isset($task['end_time']) ? Carbon::parse($task['end_time'])->endOfDay() : $start->copy()->endOfDay();
                while ($start->lte($end)) {
                    if ($start->isWeekday() && !collect(getPublicHolidays($start->year))->contains(fn($holiday) => $holiday->isSameDay($start))) {
                        $krankStageDays++;
                    }
                    $start->addDay();
                }
            }

            $montageStageDays = 0;
            foreach ($montageStage as $task) {
                $start = Carbon::parse($task['start'])->startOfDay();
                $end = isset($task['end_time']) ? Carbon::parse($task['end_time'])->endOfDay() : $start->copy()->endOfDay();
                while ($start->lte($end)) {
                    if ($start->isWeekday() && !collect(getPublicHolidays($start->year))->contains(fn($holiday) => $holiday->isSameDay($start))) {
                        $montageStageDays++;
                    }
                    $start->addDay();
                }
            }

            $userStats[$userItem->name] = [
                'urlaub_genommen' => $urlaubStageDays,
                'urlaub_verfuegbar' => $userItem->vacation_days ?? 0,
                'montage' => $montageStageDays,
                'krank' => $krankStageDays,
            ];
        }

        return view('tasks.partials.calendar', [
        'calendarData' => $filteredData->flatten(1),
        'selectedDate' => now()->format('Y-m'), // oder dein dynamisches Datum
        'userStats' => $userStats,
        'publicHolidayDays' => $this->getPublicHolidayDays(now()->format('Y-m')),
    ]);
    }

    return view('tasks.planner', [
        'calendarData' => $calendarData,
        'selectedDate' => $dateInput,
        'myUsers' => $myUsers,
        'allUsers' => $allUsers,
        'customers' => $customers,
        'categories' => $categories,
        'diagnoses' => $diagnoses,
        'nextMonthData' => $nextMonthData,
    ]);
}





    

    public function getTasks()  
    {  
        $tasks = Task::all();  

        $events = $tasks->map(function ($task) {  
            return [  
                'title' => $task->title,  
                'start' => $task->start_time->format('Y-m-d'), // Ensure the format is correct for FullCalendar  
            ];  
        });  

        return response()->json($events);  
    }  

    /**
     * Zeigt die Details einer Aufgabe.
     */
    public function show($id)
    { 
         
        // Aufgabe mit Benutzer und Kategorien laden
        //  $task->load(['user', 'categories']);
        //  return $id;
         $task = Task::with('user')->findOrFail($id);
        $users = User::where('role', 'employee')->get();


         // Lade alle Kunden


         $customers = Customer::all();
         $categories = TaskCategory::all();
 
         $task_service_forms = DB::table('table_task_service_files')->where('task_id', $id)->where('status', 1)->get();
         $task_second_service_forms = DB::table('table_task_second_service_files')->where('task_id', $id)->where('status', 1)->get();
 
         

        if (request()->ajax()) {
            return view('tasks.showmodaldata', compact('task'))->render();
        }
    
        // return redirect()->route('tasks.show');

        return view('tasks.show', compact('task', 'users','customers','categories', 'task_service_forms', 'task_second_service_forms'));
    }

    

public function edit($id)
{
    $task = Task::findOrFail($id);
    
    $users = User::where('role', 'employee')->get();
    $customers = Customer::all();
    $categories = TaskCategory::all();
    $diagnoses = Diagnosis::all();
    $selectedDiagnosisId = $task->diagnosis_id;

    $task_service_forms = DB::table('table_task_service_files')
        ->where('task_id', $id)
        ->where('status', 1)
        ->get();

    $task_second_service_forms = DB::table('table_task_second_service_files')
        ->where('task_id', $id)
        ->where('status', 1)
        ->get();
        
    // Prüfen, ob es eine AJAX-Anfrage ist (für Modal)
    if (request()->ajax()) {
        return view('tasks.partials.EditTaskModal', compact(
            'task',
            'users',
            'customers',
            'categories',
            'diagnoses',
            'selectedDiagnosisId',
            'task_service_forms',
            'task_second_service_forms'
        ));
    }

    // Normale Seite aufrufen
    return view('tasks.edit', compact(
        'task',
        'users',
        'customers',
        'categories',
        'diagnoses',
        'selectedDiagnosisId',
        'task_service_forms',
        'task_second_service_forms'
    ));
}




    /**
     * Aktualisiert eine bestehende Aufgabe.
     */
   public function update(Request $request, Task $task)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'user_id' => 'nullable|exists:users,id',
        'customer_id' => 'required|exists:customers,id',
        'start_time' => 'nullable|date',
        'end_time' => 'nullable|date|after_or_equal:start_time',
        'status' => 'required|in:open,done,not_done,billed',
        'comment' => 'nullable|string',
        'categories' => 'nullable|array',
        'categories.*' => 'exists:task_categories,id',
        'contact_id' => 'nullable|exists:contacts,id',
    ]);

    $user = User::findOrFail($validated['user_id']);
    $customer = Customer::findOrFail($validated['customer_id']);
    $task->contact_id = $request->input('contact_id');
    $pdfPath = null;
    $secondPdfPath = null;

    // ⬇️ PDFs nur generieren, wenn aus Modal gespeichert wird
    if ($request->input('request_source') === 'form') {

        if ($request->filled('service_report_id')) {
            $first_service = FirstServiceRecords::findOrFail($task->service_report_id);
            $first_service_material_consumption = DB::table('service_materials')
                ->where('first_service_record_id', $task->service_report_id)->get();
            $first_service_travel_details = DB::table('first_service_travel_details')
                ->where('first_service_record_id', $task->service_report_id)->get();

            $pdf = Pdf::loadView('tasks.pdf', [
                'modal_title' => $request->modal_title,
                'modal_description' => $request->modal_description,
                'user' => $user->name,
                'customer' => $customer,
                'service' => $first_service,
                'service_travel_details' => $first_service_travel_details,
                'service_material_consumption' => $first_service_material_consumption,
            ]);

            $fileName = 'task_' . $task->id . '_' . time() . '.pdf';
            $pdfPath = 'pdfs/' . $fileName;
            $pdf->save(public_path($pdfPath));
        }

        if ($request->filled('second_service_report_id')) {
            $service = ServiceRecord::findOrFail($request->second_service_report_id);
            $service_material_consumption = DB::table('task_reports_material_consumption')
                ->where('service_record_id', $request->second_service_report_id)->get();
            $service_travel_details = DB::table('service_travel_details')
                ->where('service_record_id', $request->second_service_report_id)->get();

            $pdf2 = Pdf::loadView('tasks.pdf', [
                'modal_title' => $request->modal_title,
                'modal_description' => $request->modal_description,
                'user' => $user->name,
                'customer' => $customer,
                'service' => $service,
                'service_travel_details' => $service_travel_details,
                'service_material_consumption' => $service_material_consumption,
            ]);

            $secondFileName = 'second_task_' . $task->id . '_' . time() . '.pdf';
            $secondPdfPath = 'second_pdfs/' . $secondFileName;
            $pdf2->save(public_path($secondPdfPath));
        }
    }

    // 🛠️ Task aktualisieren
    $task->fill([
        'title' => $validated['title'],
        'user_id' => $validated['user_id'],
        'customer_id' => $validated['customer_id'],
        'status' => $validated['status'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'comment' => $validated['comment'],
        'modal_title' => $request->modal_title ?? '',
        'modal_description' => $request->modal_description ?? '',

    ]);

    if ($pdfPath) {
        $task->pdf_path = $pdfPath;
    }

    if ($secondPdfPath) {
        $task->second_pdf_path = $secondPdfPath;
    }

    if ($request->filled('service_report_id')) {
        $task->service_report_id = $request->service_report_id;
    }

    if ($request->filled('second_service_report_id')) {
        $task->second_service_report_id = $request->second_service_report_id;
    }

    $task->save();

    // 🧹 Altes Auftrag-PDF ersetzen, nur wenn kein Urlaub/Krank
    $isVacationOrSick = false;
    if ($request->has('categories')) {
        $urlaubId = TaskCategory::where('name', 'Urlaub')->first()?->id;
        $krankId = TaskCategory::where('name', 'Krank')->first()?->id;
        $selected = $request->input('categories', []);
        $isVacationOrSick = in_array($urlaubId, $selected) || in_array($krankId, $selected);
    }

    if (!$isVacationOrSick) {
        if ($task->pdf_path && file_exists(public_path($task->pdf_path))) {
            unlink(public_path($task->pdf_path));
        }

        $taskWithRelations = Task::with(['customer', 'contact', 'user'])->find($task->id);
        $auftragPdf = Pdf::loadView('tasks.pdf_auftrag', ['task' => $taskWithRelations]);
        $auftragName = 'auftrag_' . $task->id . '_' . time() . '.pdf';
        $auftragPath = 'pdfs/' . $auftragName;
        $auftragPdf->save(public_path($auftragPath));

        $task->pdf_path = $auftragPath;
        $task->save();
    }

    // Kategorien synchronisieren
    if (!empty($validated['categories'])) {
        $task->categories()->sync($validated['categories']);
    }

    if ($request->ajax()) {
        return response()->json(['message' => 'Aufgabe erfolgreich gespeichert.']);
    }

    return redirect()->route('tasks.edit', ['id' => $task->id])->with('success', 'Aufgabe erfolgreich gespeichert.');
}



    

    /**
     * Löscht eine Aufgabe aus der Datenbank.
     */
    public function destroy($id)
{
    $task = Task::find($id);

    if ($task) {
        DB::table('table_task_service_files')->where('task_id', $task->id)->delete();
        DB::table('table_task_second_service_files')->where('task_id', $task->id)->delete();
        $task->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Aufgabe erfolgreich gelöscht.']);
        }
    }

    return redirect()->route('tasks.index')->with('success', 'Aufgabe erfolgreich gelöscht.');
}




public function assignToUser(Request $request)
{
    $task = Task::findOrFail($request->task_id);
    $user = User::where('name', $request->user_name)->firstOrFail();

    $date = Carbon::parse($request->date);

    $task->user_id = $user->id;
    $task->start_time = $date->startOfDay();
    $task->end_time = $date->endOfDay();
    $task->save();

    return response()->json(['message' => 'Task assigned']);
}


public function unassignedTasks()
{
    $tasks = Task::with('categories')
        ->whereNull('user_id')
        ->get();

    return response()->json($tasks);
}



public function assign(Request $request, $id)
{
    $task = Task::findOrFail($id);

    $user = User::where('name', $request->input('user_name'))->first();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Mitarbeiter nicht gefunden.']);
    }

    $newStart = Carbon::parse($request->input('date'));

    // Dauer berechnen (Standard: 0 Tage)
    $durationInDays = $task->start_time && $task->end_time
        ? $task->start_time->diffInDays($task->end_time)
        : 0;

    $newEnd = $newStart->copy()->addDays($durationInDays);

    $task->user_id = $user->id;
    $task->start_time = $newStart;
    $task->end_time = $newEnd;
    $task->save();

    return response()->json(['success' => true]);
}






    private function generateOrderNumber()
{
    $year = now()->year;

    // Suche die höchste bestehende Auftragsnummer aus diesem Jahr
    $lastTask = Task::whereYear('created_at', $year)
        ->whereNotNull('order_number')
        ->orderByDesc('order_number')
        ->first();

    if ($lastTask) {
        // Extrahiere die Nummer aus der letzten Order-Number
        $parts = explode('-AN-', $lastTask->order_number);
        $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
    } else {
        $lastNumber = 0;
    }

    // Nummer hochzählen
    $newNumber = $lastNumber + 1;

    // Formatieren: immer 4 Stellen (0001, 0002, etc.)
    $newNumberFormatted = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    // Zusammenbauen
    return $year . '-AN-' . $newNumberFormatted;
}

private function getCalendarData()
{
    $tasks = Task::with(['user', 'categories'])->get();
    $users = User::all();

    $calendarData = [];

    foreach ($users as $user) {
        $userTasks = $tasks->filter(fn($task) => $task->user_id === $user->id);

        foreach ($userTasks as $task) {
            $calendarData[] = [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->start_time,
                'end_time' => $task->end_time,
                'user' => $user->name,
                'description' => $task->description,
                'icon' => $task->categories->first()?->icon,
                'color_picker' => $task->color_picker ?? '#ffffff',
                'categories' => $task->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray(),
            ];
        }
    }

    return $calendarData;
}






    
}
