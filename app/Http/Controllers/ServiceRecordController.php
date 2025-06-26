<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRecord;
use App\Models\Task;
use App\Models\FirstServiceRecords;
use App\Models\FirstServiceTravelDetail;
use App\Models\ServiceMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Customer;
use DB;

class ServiceRecordController extends Controller
{
    // Display a form for creating a new service record
    public function create()
    {
        return view('service-records.create', compact('tasks', 'users', 'customers'));
    }

    

    public function store(Request $request)
{
    try {
        // Eingabe validieren
        $validated = $request->validate([
            'auftraggeber' => 'string|nullable',
            'ansprechpartner' => 'string|nullable',
            'telefon' => 'string|nullable',
            'service_beleg_nr' => 'string|nullable',
            'ab_nr' => 'string|nullable',
            'rekla_sa_nr' => 'string|nullable',
            'debit_nr' => 'string|nullable',
            'reparatur' => 'boolean',
            'rep_aufnahme' => 'boolean',
            'wartung' => 'boolean',
            'schulung' => 'boolean',
            'auslieferung' => 'boolean',
            'bfk' => 'boolean',
            'kb' => 'boolean',
            'pb' => 'boolean',
            'nt' => 'boolean',
            'km' => 'boolean',
            'sonstiges' => 'boolean',
            'typ' => 'string|nullable',
            'serien_nr' => 'string|nullable',
            'funktion_in_ordnung' => 'boolean',
            'funktion_nicht_in_ordnung' => 'boolean',
            'materialconslieferung.*' => 'string|nullable',
            'art_no_nr.*' => 'string|nullable',
            'bemerkungen' => 'string|nullable',
            'beschreibung' => 'string|nullable',
            'datum_material.*' => 'date|nullable',
            'anfahrtzeit.*' => 'string|nullable',
            'ruckfahrtzeit.*' => 'string|nullable',
            'fahrt_km.*' => 'integer|nullable',
            'pausch_anfahrt.*' => 'integer|nullable',
            'wartezeit.*' => 'integer|nullable',
            'arbeitszeit.*' => 'integer|nullable',
            'ges_arbeitszeit.*' => 'integer|nullable',
            'personenzahl.*' => 'integer|nullable',
            'hotel_ubernachtung' => 'boolean',
            'hotel_von' => 'string|nullable',
            'hotel_bis' => 'string|nullable',
            'arbeit_fertig' => 'boolean',
            'kostenpflichtig' => 'boolean',
            'unter_vorbehalt' => 'boolean',
            'sign_date' => 'date|nullable',
            'kunde_name' => 'string|nullable',
            'task_id' => 'integer|nullable',
            'user_id' => 'integer|nullable',
            'customer_id' => 'integer|nullable',
        ]);

        // Array-Felder entfernen
        $keysToUnset = [
            'materialconslieferung', 'art_no_nr', 'datum_material',
            'anfahrtzeit', 'ruckfahrtzeit', 'fahrt_km',
            'pausch_anfahrt', 'wartezeit', 'arbeitszeit',
            'ges_arbeitszeit', 'personenzahl'
        ];
        foreach ($keysToUnset as $key) {
            unset($validated[$key]);
        }

        // Task-Infos laden
        $taskTitle = Task::find($request->task_id);
        \Log::info('Task Title Retrieval', [
            'task_id' => $request->task_id,
            'task_title' => $taskTitle ? $taskTitle->title : 'Not Found'
        ]);

        // Signaturen speichern
        if ($request->techniker_name) {
            $validated['techniker_name'] = $this->saveSignature($request->techniker_name, 'techniker_name');
        }
        if ($request->kunde_signature) {
            $validated['kunde_signature'] = $this->saveSignature($request->kunde_signature, 'kunde_signature');
        }

        // Service-Record anlegen
        $serviceRecord = FirstServiceRecords::create($validated);

        // Materialien speichern
        foreach ($request->materialconslieferung ?? [] as $key => $material) {
            if (empty($material) && empty($request->art_no_nr[$key] ?? null)) {
                continue;
            }
            $materialId = DB::table('service_materials')->insertGetId([
                'first_service_record_id' => $serviceRecord->id,
                'piece_stuck' => $material,
                'art_no_nr' => $request->art_no_nr[$key] ?? null,
            ]);
            \Log::info('Material Record Inserted', [
                'material_id' => $materialId,
                'piece_stuck' => $material,
                'art_no_nr' => $request->art_no_nr[$key] ?? null
            ]);
        }

        // Fahrtdetails speichern
        foreach ($request->datum_material ?? [] as $key => $datum) {
            if (empty($datum)) continue;

            $travelId = DB::table('first_service_travel_details')->insertGetId([
                'first_service_record_id' => $serviceRecord->id,
                'datum_material' => $datum,
                'anfahrtzeit' => $request->anfahrtzeit[$key] ?? null,
                'ruckfahrtzeit' => $request->ruckfahrtzeit[$key] ?? null,
                'fahrt_km' => $request->fahrt_km[$key] ?? null,
                'pausch_anfahrt' => $request->pausch_anfahrt[$key] ?? null,
                'wartezeit' => $request->wartezeit[$key] ?? null,
                'arbeitszeit' => $request->arbeitszeit[$key] ?? null,
                'ges_arbeitszeit' => $request->ges_arbeitszeit[$key] ?? null,
                'personenzahl' => $request->personenzahl[$key] ?? null,
            ]);
            \Log::info('Travel Detail Inserted', [
                'travel_id' => $travelId,
                'datum_material' => $datum
            ]);
        }

        // PDF erzeugen
        $pdf = \Pdf::loadView('tasks.pdf', [
            'modal_title' => $taskTitle->title ?? '',
            'modal_description' => $taskTitle->comment ?? '',
            'user' => User::find($request->user_id)?->name ?? '',
            'customer' => Customer::find($request->customer_id)?->company_name ?? '',
            'service' => $serviceRecord,
            'service_material_consumption' => DB::table('service_materials')->where('first_service_record_id', $serviceRecord->id)->get(),
            'service_travel_details' => DB::table('first_service_travel_details')->where('first_service_record_id', $serviceRecord->id)->get(),
            'notizen' => $serviceRecord->notizen ?? '',
        ]);

        // PDF speichern, wenn task_id vorhanden
        if ($request->task_id) {
            $fileName = 'task_' . time() . '.pdf';
            $filePath = 'pdfs/' . $fileName;

            if (!file_exists(public_path('pdfs'))) {
                mkdir(public_path('pdfs'), 0775, true);
            }

            $fullPath = public_path($filePath);
            $pdf->save($fullPath);

            if (!file_exists($fullPath)) {
                \Log::error('PDF file missing after save', ['path' => $fullPath]);
                throw new \Exception('PDF konnte nicht gespeichert werden');
            }

            \Log::info('PDF saved successfully.', ['path' => $filePath]);

            $fileId = DB::table('table_task_service_files')->insertGetId([
                'task_id' => $request->task_id,
                'first_services_id' => $serviceRecord->id,
                'first_service_file' => $filePath,
                'status' => 1
            ]);
            \Log::info('Service File Record Inserted', ['file_id' => $fileId]);
        }

        \Log::info('Service Record Creation Successful', [
            'service_id' => $serviceRecord->id,
            'file_path' => $filePath ?? 'No PDF'
        ]);

        if ($request->has('request_source') && $request->request_source === 'form') {
            Session::put('success', 'Service record created successfully.');
            return redirect()->back();
        }

        return response()->json([
            'message' => 'Service record created successfully.',
            'data' => $serviceRecord,
            'path' => $filePath ?? '',
            'service_id' => $serviceRecord->id,
            'flag' => 'add',
        ]);

    } catch (\Exception $e) {
        \Log::error('Service Record Creation Failed', [
            'error_message' => $e->getMessage(),
            'error_line' => $e->getLine(),
        ]);

        if ($request->has('request_source') && $request->request_source === 'form') {
            return redirect()->back()->withInput()->withErrors(['error' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()]);
        }

        return response()->json(['error' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()], 500);
    }
}


    private function saveSignature($base64String, $filename)
    {
        $imageData = str_replace('data:image/png;base64,', '', $base64String);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = 'signatures/' . $filename . '_' . time() . '.png';
        Storage::disk('public')->put($imageName, base64_decode($imageData));
        return $imageName;
    }

    // Display a listing of the service records
    public function index()
    {
        $serviceRecords = ServiceRecord::all();
        return view('service-records.index', compact('serviceRecords'));
    }

    // Show a single service record
    public function show($id)
    {
        $serviceRecord = ServiceRecord::findOrFail($id);
        return view('service-records.show', compact('serviceRecord'));
    }

    // Display a form for editing a service record
    public function createServiceModal()
    {
        return view('tasks.pdfform_generate')->render();
    }

    // Display a form for editing a service record
    public function createSecondServiceModal(Request $request)
{
    $task = null;
    $customer = null;

    if ($request->has('task_id')) {
        $task = Task::with('customer')->find($request->get('task_id'));
        $customer = $task?->customer;
    }

    $users = User::select('id', 'name')->get();
    $tasks = Task::select('id', 'title')->get();
    $customers = Customer::select('id', 'company_name')->get();

    return view('tasks.newAddModal', compact('task', 'users', 'tasks', 'customers', 'customer'))->render();
}



    // Display a form for editing a service record
    public function edit($id)
    {
        $first_service = FirstServiceRecords::findOrFail($id);
        $task = Task::find($first_service->task_id);
        $customer = Customer::find($first_service->customer_id);
        $first_service_material_consumption = DB::table('service_materials')->where('first_service_record_id', $id)->get();
        $first_service_travel_details = DB::table('first_service_travel_details')->where('first_service_record_id', $id)->get();

        return view('tasks.edit_pdfform_generate', compact(
            'first_service',
            'task',
            'customer',
            'first_service_material_consumption',
            'first_service_travel_details'
        ))->render();
    }


    // Display a form for editing a service record
    public function editSecondForm($id)
    {
        // $serviceRecord = ServiceRecord::findOrFail($id);
        $service = ServiceRecord::find($id);
        $service_material_consumption = DB::table('task_reports_material_consumption')->where('service_record_id', $id)->get();
        $service_travel_details = DB::table('service_travel_details')->where('service_record_id', $id)->get();

        return view('tasks.newEditModal', compact('service', 'service_material_consumption', 'service_travel_details'))->render();
    }

    public function getTasks()
    {
        $tasks = Task::select('id', 'title')->get();
        return response()->json($tasks);
    }

    public function getUsers()
    {
        $users = User::select('id', 'name')->get();
        return response()->json($users);
    }

    public function getCustomers()
    {
        $customers = Customer::select('id', 'company_name')->get();
        return response()->json($customers);
    }

    
    public function update(Request $request, $id)
    {
        try {
            // Log incoming request
    
            // Validate request
            $validated = $request->validate([
                'auftraggeber' => 'string|nullable',
                'ansprechpartner' => 'string|nullable',
                'telefon' => 'string|nullable',
                'service_beleg_nr' => 'string|nullable',
                'ab_nr' => 'string|nullable',
                'rekla_sa_nr' => 'string|nullable',
                'debit_nr' => 'string|nullable',
                'reparatur' => 'boolean',
                'rep_aufnahme' => 'boolean',
                'wartung' => 'boolean',
                'schulung' => 'boolean',
                'auslieferung' => 'boolean',
                'bfk' => 'boolean',
                'kb' => 'boolean',
                'pb' => 'boolean',
                'nt' => 'boolean',
                'km' => 'boolean',
                'sonstiges' => 'boolean',
                'typ' => 'string|nullable',
                'serien_nr' => 'string|nullable',
                'funktion_in_ordnung' => 'boolean',
                'funktion_nicht_in_ordnung' => 'boolean',
                'materialconslieferung.*' => 'string|nullable',
                'art_no_nr.*' => 'string|nullable',
                'bemerkungen' => 'string|nullable',
                'beschreibung' => 'string|nullable',
                'datum_material.*' => 'date|nullable',
                'anfahrtzeit.*' => 'string|nullable',
                'ruckfahrtzeit.*' => 'string|nullable',
                'fahrt_km.*' => 'integer|nullable',
                'pausch_anfahrt.*' => 'integer|nullable',
                'wartezeit.*' => 'integer|nullable',
                'arbeitszeit.*' => 'integer|nullable',
                'ges_arbeitszeit.*' => 'integer|nullable',
                'personenzahl.*' => 'integer|nullable',
                'hotel_ubernachtung' => 'boolean',
                'hotel_von' => 'string|nullable',
                'hotel_bis' => 'string|nullable',
                'arbeit_fertig' => 'boolean',
                'kostenpflichtig' => 'boolean',
                'unter_vorbehalt' => 'boolean',
                'sign_date' => 'date|nullable',
                'kunde_name' => 'string|nullable',
                'task_id' => 'integer|nullable',
                'user_id' => 'integer|nullable',
                'customer_id' => 'integer|nullable',
            ]);
    
            // Unset array fields
            $keysToUnset = [
                'materialconslieferung',
                'art_no_nr',
                'datum_material',
                'anfahrtzeit',
                'ruckfahrtzeit',
                'fahrt_km',
                'pausch_anfahrt',
                'wartezeit',
                'arbeitszeit',
                'ges_arbeitszeit',
                'personenzahl'
            ];
            foreach ($keysToUnset as $key) {
                unset($validated[$key]);
            }
    
            // Fetch service record
            $serviceRecord = FirstServiceRecords::findOrFail($id);
    
            // Fetch task
            $taskTitle = Task::find($request->task_id);
            Log::info('Task Title Retrieval', [
                'task_id' => $request->task_id,
                'task_title' => $taskTitle ? $taskTitle->title : 'Not Found'
            ]);
    
            // Handle signatures
            if ($request->techniker_name) {
                $validated['techniker_name'] = $this->saveSignature($request->techniker_name, 'techniker_name');
            }
            if ($request->kunde_signature) {
                $validated['kunde_signature'] = $this->saveSignature($request->kunde_signature, 'kunde_signature');
            }
    
            // Update service record
            $serviceRecord->update($validated);
    
            // Delete existing materials and travel details
            DB::table('service_materials')->where('first_service_record_id', $serviceRecord->id)->delete();
            DB::table('first_service_travel_details')->where('first_service_record_id', $serviceRecord->id)->delete();
    
            // Insert new materials (skip empty rows)
            foreach ($request->materialconslieferung ?? [] as $key => $material) {
                if (empty($material) && empty($request->art_no_nr[$key] ?? null)) {
                    continue;
                }
                $materialId = DB::table('service_materials')->insertGetId([
                    'first_service_record_id' => $serviceRecord->id,
                    'piece_stuck' => $material,
                    'art_no_nr' => $request->art_no_nr[$key] ?? null,
                ]);
                Log::info('Material Record Inserted', [
                    'material_id' => $materialId,
                    'piece_stuck' => $material,
                    'art_no_nr' => $request->art_no_nr[$key] ?? null
                ]);
            }
    
            // Insert new travel details
            foreach ($request->datum_material ?? [] as $key => $datum) {
                if (empty($datum)) {
                    continue;
                }
                $travelId = DB::table('first_service_travel_details')->insertGetId([
                    'first_service_record_id' => $serviceRecord->id,
                    'datum_material' => $datum,
                    'anfahrtzeit' => $request->anfahrtzeit[$key] ?? null,
                    'ruckfahrtzeit' => $request->ruckfahrtzeit[$key] ?? null,
                    'fahrt_km' => $request->fahrt_km[$key] ?? null,
                    'pausch_anfahrt' => $request->pausch_anfahrt[$key] ?? null,
                    'wartezeit' => $request->wartezeit[$key] ?? null,
                    'arbeitszeit' => $request->arbeitszeit[$key] ?? null,
                    'ges_arbeitszeit' => $request->ges_arbeitszeit[$key] ?? null,
                    'personenzahl' => $request->personenzahl[$key] ?? null,
                ]);
                Log::info('Travel Detail Inserted', [
                    'travel_id' => $travelId,
                    'datum_material' => $datum,
                    'anfahrtzeit' => $request->anfahrtzeit[$key] ?? null
                ]);
            }
    
            // Generate unique file name to avoid race condition
            
            $fileName = 'task_' . time() . '.pdf';
            $filePath = 'pdfs/' . $fileName;
    
            // Generate PDF
            $pdf = Pdf::loadView('pdf.service_second', [
                'modal_title' => $taskTitle ? $taskTitle->title : '',
                'modal_description' => $taskTitle ? $taskTitle->comment : '',
                'user' => User::find($request->user_id)?->name ?? '',
                'customer' => Customer::find($request->customer_id)?->company_name ?? '',
                'service' => $serviceRecord,
                'service_material_consumption' => DB::table('service_materials')->where('first_service_record_id', $serviceRecord->id)->get(),
                'service_travel_details' => DB::table('first_service_travel_details')->where('first_service_record_id', $serviceRecord->id)->get(),
            ]);
    
            // Save PDF if task_id exists
            if ($request->task_id) {
    $fileName = 'task_' . time() . '.pdf';
    $filePath = 'pdfs/' . $fileName;

    if (!file_exists(public_path('pdfs'))) {
        mkdir(public_path('pdfs'), 0775, true);
    }

    $fullPath = public_path($filePath);
    $pdf->save($fullPath);

    if (!file_exists($fullPath)) {
        Log::error('PDF file missing after save', ['path' => $fullPath]);
        throw new \Exception('PDF konnte nicht gespeichert werden');
    }

    Log::info('PDF saved successfully.', ['path' => $filePath]);

    // PDF in Datenbank speichern
    $fileId = DB::table('table_task_service_files')->insertGetId([
        'task_id' => $request->task_id,
        'first_services_id' => $serviceRecord->id,
        'first_service_file' => $filePath,
        'status' => 1
    ]);

    Log::info('Service File Record Inserted', ['file_id' => $fileId]);
    Log::info('Task ID on store:', ['task_id' => $request->task_id]);
}

\Log::info('Service Record Creation Successful', [
    'service_id' => $serviceRecord->id,
    'file_path' => $filePath ?? 'No PDF'
]);

if ($request->has('request_source') && $request->request_source === 'form') {
    Session::put('success', 'Service record created successfully.');
    return redirect()->back();
}

return response()->json([
    'message' => 'Service record created successfully.',
    'data' => $serviceRecord,
    'path' => $filePath ?? '',
    'service_id' => $serviceRecord->id,
    'flag' => 'add',
]);

    } catch (\Exception $e) {
    \Log::error('Service Record Creation Failed', [
        'error_message' => $e->getMessage(),
        'error_line' => $e->getLine(),
    ]);

    if ($request->has('request_source') && $request->request_source === 'form') {
        return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
    }

    return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
} 
}

   
    public function secondFromUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'auftraggeber' => 'string|nullable',
            'auftr_nr' => 'string|nullable',
            'kostenst' => 'string|nullable',
            'aart_der_ausgefuhrten' => 'string|nullable',
            'rsl' => 'string|nullable',
            'iso' => 'string|nullable',
            'iea' => 'string|nullable',
            'ansprechpartner' => 'string|nullable',
            'telefon' => 'string|nullable',
            'service_beleg_nr' => 'string|nullable',
            'ab_nr' => 'string|nullable',
            'rekla_sa_nr' => 'string|nullable',
            'debit_nr' => 'string|nullable',
            'reklamation' => 'boolean',
            'reparatur' => 'boolean',
            'rep_aufnahme' => 'boolean',
            'wartung' => 'boolean',
            'schulung' => 'boolean',
            'auslieferung' => 'boolean',
            'STK_bestanden_nein' => 'boolean',
            'funktiontest_besttanden_nein' => 'boolean',
            'bfk' => 'boolean',
            'kb' => 'boolean',
            'pb' => 'boolean',
            'nt' => 'boolean',
            'km' => 'boolean',
            'sonstiges' => 'boolean',
            'typ' => 'string|nullable',
            'serien_nr' => 'string|nullable',
            'funktion_in_ordnung' => 'boolean',
            'funktion_nicht_in_ordnung' => 'boolean',
            'materialconslieferung.*' => 'string|nullable',
            'Beschreibung.*' => 'string|nullable',
            'art_nr.*' => 'string|nullable',
            'bemerkungen' => 'string|nullable',
            'beschreibung' => 'string|nullable',
            'datum_material.*' => 'date|nullable',
            'anfahrtzeit.*' => 'string|nullable',
            'ruckfahrtzeit.*' => 'string|nullable',
            'fahrt_km.*' => 'integer|nullable',
            'pausch_anfahrt.*' => 'integer|nullable',
            'wartezeit.*' => 'integer|nullable',
            'arbeitszeit.*' => 'integer|nullable',
            'ges_arbeitszeit.*' => 'integer|nullable',
            'personenzahl.*' => 'integer|nullable',
            'ges_arbeitszeit_tag.*' => 'string|nullable',
            'ges_arbeitszeit_monat.*' => 'string|nullable',
            'fahrt_km_zurück.*' => 'string|nullable',
            'hotel_ubernachtung' => 'boolean',
            'hotel_von' => 'string|nullable',
            'hotel_bis' => 'string|nullable',
            'arbeit_fertig' => 'boolean',
            'funktiontest_besttanden_ja' => 'boolean',
            'kostenpflichtig_nein' => 'boolean',
            'kostenpflichtig' => 'boolean',
            'unter_vorbehalt' => 'boolean',
            'sign_date' => 'date|nullable',
            'techniker_name' => 'string|nullable',
            'kunde_unterschrift' => 'string|nullable',
            'arbeit_fertig_nein' => 'boolean',
            'STK_bestanden_ja' => 'boolean',
            'notizen' => 'string|nullable',

        ]);

        // Create the new service record
        $keysToUnset = [
            "materialconslieferung",
            "art_nr",
            "datum_material",
            "anfahrtzeit",
            "ruckfahrtzeit",
            "fahrt_km",
            "pausch_anfahrt",
            "wartezeit",
            "arbeitszeit",
            "ges_arbeitszeit",
            "personenzahl",
            "datum_material_tag",
            "datum_material_monat",
            "anfahrtzeit_von",
            "anfahrtzeit_bis",
            "anfahrtzeit_std",
            "ruckfahrtzeit_von",
            "ruckfahrtzeit_bis",
            "ruckfahrtzeit_std",
            "fahrt_km_hin",
            "fahrt_km_zurück",
            "ges_arbeitszeit_tag",
            "ges_arbeitszeit_monat",
            "Beschreibung",
        ];

        // Loop through each key to unset
        foreach ($keysToUnset as $key) {
            unset($validated[$key]);
        }
        $taskTitle = Task::find($request->task_id);

        if ($id == 0) {
            if ($request->kunde_name) {
                $validated['kunde_name'] = $this->saveSignature($request->kunde_name, 'kunde_name');
            }

            $serviceRecord = ServiceRecord::create($validated);

            // Insert material consumption
            foreach ($request->materialconslieferung_second ?? [] as $key => $material) {
                DB::table('task_reports_material_consumption')->insertGetId([
                    'service_record_id' => $serviceRecord->id,
                    'piece_stück' => $request->materialconslieferung_second[$key],
                    'Beschreibung' => $request->Beschreibung_second[$key],
                    'art_no_nr' => $request->art_no_nr_second[$key],
                ]);
            }

            // Insert travel details
            foreach ($request->datum_material_second ?? [] as $travelkey => $datum) {
            DB::table('service_travel_details')->where('id', $request->travelDetailId_second[$travelkey])->update([
                'datum_material' => $datum,
                'anfahrtzeit_von' => $request->anfahrtzeit_von_second[$travelkey] ?? '',
                'anfahrtzeit_bis' => $request->anfahrtzeit_bis_second[$travelkey] ?? '',
                'anfahrtzeit_std' => $request->anfahrtzeit_std_second[$travelkey] ?? '',
                'ruckfahrtzeit_von' => $request->ruckfahrtzeit_von_second[$travelkey] ?? '',
                'ruckfahrtzeit_bis' => $request->ruckfahrtzeit_bis_second[$travelkey] ?? '',
                'ruckfahrtzeit_std' => $request->ruckfahrtzeit_std_second[$travelkey] ?? '',
                'fahrt_km_hin' => $request->fahrt_km_hin_second[$travelkey] ?? '',
                'fahrt_km_zurück' => $request->fahrt_km_zurück_second[$travelkey] ?? '',
                'pausch_anfahrt' => $request->pausch_anfahrt_second[$travelkey] ?? '',
                'wartezeit' => $request->wartezeit_second[$travelkey] ?? '',
                'ges_arbeitszeit' => $request->ges_arbeitszeit_second[$travelkey] ?? '',
                'ges_arbeitszeit_tag' => $request->ges_arbeitszeit_tag_second[$travelkey] ?? '',
                'ges_arbeitszeit_monat' => $request->ges_arbeitszeit_monat_second[$travelkey] ?? '',
            ]);
        }

            // Fetch service details for PDF
            $service = ServiceRecord::findOrFail($serviceRecord->id);
            $service_material_consumption = DB::table('task_reports_material_consumption')
                ->where('service_record_id', $serviceRecord->id)
                ->get();
            $service_travel_details = DB::table('service_travel_details')
                ->where('service_record_id', $serviceRecord->id)
                ->get();

            $user = User::findOrFail($request->user_id);
            $customer = Customer::findOrFail($request->customer_id);

            // Generate PDF
            $pdf2 = Pdf::loadView('tasks.pdf', [
                'modal_title' => $taskTitle->title ?? '',
                'modal_description' => $taskTitle->comment ?? '',
                'user' => $user->name,
                'customer' => $customer->company_name,
                'service' => $service,
                'service_travel_details' => $service_travel_details,
                'service_material_consumption' => $service_material_consumption,
            ]);


            if ($request->task_id != 0) {
                $secondfileName = 'task_' . time() . '.pdf';
                $secondPdfPath = 'second_pdfs/' . $secondfileName;

                try {
                    $pdf2->save(public_path($secondPdfPath));
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to save PDF'], 500);
                }

                // Save PDF path in database
                DB::table('table_task_second_service_files')->insertGetId([
                    'task_id' => $request->task_id,
                    'service_id' => $serviceRecord->id,
                    'second_service_file' => $secondPdfPath,
                ]);
            }

            return response()->json([
                'message' => 'Service record created successfully.',
                'flag' => 'add',
                'data' => $serviceRecord,
                'service_id' => $serviceRecord->id,
                'service_file' => $secondPdfPath ?? '',
            ]);
        } else {
            $serviceRecord = ServiceRecord::findOrFail($id);
            $serviceRecord->update($validated);

            // Update material consumption
            foreach ($request->materialconslieferung_second as $key => $material) {
                DB::table('task_reports_material_consumption')->where('id', $request->materialId_second[$key])->update([
                    'service_record_id' => $serviceRecord->id,
                    'piece_stück' => $request->materialconslieferung_second[$key],
                    'Beschreibung' => $request->Beschreibung_second[$key],
                    'art_no_nr' => $request->art_no_nr_second[$key] ?? '',
                ]);
            }

            // Update travel details
            foreach ($request->datum_material_tag_second as $travelkey => $travel_detail) {
                DB::table('service_travel_details')->where('id', $request->travelDetailId_second[$travelkey])->update([
                    'service_record_id' => $serviceRecord->id,
                    'datum_material_tag' => $request->datum_material_tag_second[$travelkey],
                    'datum_material_monat' => $request->datum_material_monat_second[$travelkey],
                    'anfahrtzeit_von' => $request->anfahrtzeit_von_second[$travelkey],
                    'anfahrtzeit_bis' => $request->anfahrtzeit_bis_second[$travelkey],
                    'anfahrtzeit_std' => $request->anfahrtzeit_std_second[$travelkey],
                    'ruckfahrtzeit_von' => $request->ruckfahrtzeit_von_second[$travelkey],
                    'ruckfahrtzeit_bis' => $request->ruckfahrtzeit_bis_second[$travelkey],
                    'ruckfahrtzeit_std' => $request->ruckfahrtzeit_std_second[$travelkey],
                    'fahrt_km_hin' => $request->fahrt_km_hin_second[$travelkey],
                    'fahrt_km_zurück' => $request->fahrt_km_zurück_second[$travelkey],
                    'pausch_anfahrt' => $request->pausch_anfahrt_second[$travelkey],
                    'wartezeit' => $request->wartezeit_second[$travelkey],
                    'ges_arbeitszeit' => $request->ges_arbeitszeit_second[$travelkey],
                    'ges_arbeitszeit_tag' => $request->ges_arbeitszeit_tag_second[$travelkey],
                    'ges_arbeitszeit_monat' => $request->ges_arbeitszeit_monat_second[$travelkey],
                ]);
            }

            // Check and delete old PDF if it exists
            $oldPdfPath = DB::table('table_task_second_service_files')
                ->where('service_id', $serviceRecord->id)
                ->value('second_service_file');

            if ($oldPdfPath && file_exists(public_path($oldPdfPath))) {
                try {
                    unlink(public_path($oldPdfPath));
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to delete old PDF'], 500);
                }
            }

            // Generate new PDF
            $user = User::findOrFail($request->user_id);
            $customer = Customer::findOrFail($request->customer_id);

            $service_material_consumption = DB::table('task_reports_material_consumption')
                ->where('service_record_id', $serviceRecord->id)
                ->get();
            $service_travel_details = DB::table('service_travel_details')
                ->where('service_record_id', $serviceRecord->id)
                ->get();

            $pdf2 = Pdf::loadView('tasks.pdf', [
                'modal_title' => $taskTitle->title ?? '',
                'modal_description' => $taskTitle->comment ?? '',
                'user' => $user->name,
                'customer' => $customer->company_name,
                'service' => $serviceRecord,
                'service_travel_details' => $service_travel_details,
                'service_material_consumption' => $service_material_consumption,
            ]);

            // Save new PDF
            $secondfileName = 'task_' . time() . '.pdf';
            $secondPdfPath = 'second_pdfs/' . $secondfileName;

            try {
                $pdf2->save(public_path($secondPdfPath));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to save new PDF'], 500);
            }

            // Update PDF path in database
            $existingRecord = DB::table('table_task_second_service_files')
                ->where('service_id', $serviceRecord->id)
                ->first();

            if ($existingRecord) {
                DB::table('table_task_second_service_files')
                    ->where('service_id', $serviceRecord->id)
                    ->update(['second_service_file' => $secondPdfPath]);
            } else {
                DB::table('table_task_second_service_files')->insertGetId([
                    'task_id' => $request->task_id,
                    'service_id' => $serviceRecord->id,
                    'second_service_file' => $secondPdfPath,
                ]);
            }

            return response()->json([
                'message' => 'Service record updated successfully.',
                'data' => $serviceRecord,
                'flag' => 'edit',
                'service_file' => $secondPdfPath,
            ]);
        }
    }
    // Delete a service record
    // public function destroy($id)
    // {
    //     // $serviceRecord = ServiceRecord::findOrFail($id);
    //     // $serviceRecord->delete();

    //     // $task = Task::where('service_report_id', $id)->first();
    //     // $task->pdf_path = '';
    //     // $task->save();

    //     $delete_service = DB::table('table_task_service_files')->where('first_services_id', $id)->update([
    //         'status' => 0,
    //     ]);

    //     $task = DB::table('table_task_service_files')->where('first_services_id', $id)->first();

    //     // return redirect()->route('service-records.index')->with('success', 'Service Record deleted successfully');
    //     return redirect()->route('tasks.edit', $task->task_id)->with('success', 'Service Record deleted successfully');
    // }

    public function destroy($id)
    {
        try {
            Log::info('Destroy Method Called', ['first_services_id' => $id]);

            // Verify record exists
            $serviceFile = DB::table('table_task_service_files')
                ->where('first_services_id', $id)
                ->where('status', 1)
                ->first();

            if (!$serviceFile) {
                Log::warning('Service file not found or already deleted', [
                    'first_services_id' => $id,
                    'query' => DB::table('table_task_service_files')->where('first_services_id', $id)->toSql()
                ]);
                return redirect()->back()->with('error', 'Service record not found or already deleted.');
            }

            // Update status to 0 (soft delete)
            $updated = DB::table('table_task_service_files')
                ->where('first_services_id', $id)
                ->update(['status' => 0]);

            if ($updated) {
                Log::info('Service file soft deleted', [
                    'first_services_id' => $id,
                    'task_id' => $serviceFile->task_id
                ]);
                return redirect()->route('tasks.edit', $serviceFile->task_id)
                    ->with('success', 'Bericht erfolgreich gelöscht');
            } else {
                Log::error('Failed to update service file status', ['first_services_id' => $id]);
                return redirect()->back()->with('error', 'Failed to delete service record.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete service record', [
                'first_services_id' => $id,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return redirect()->back()->with('error', 'Failed to delete service record: ' . $e->getMessage());
        }
    }

    // Delete a service record
    // public function secondDestroy($id)
    // {
    //     // $serviceRecord = ServiceRecord::findOrFail($id);
    //     // $serviceRecord->delete();

    //     // $task = Task::where('second_service_report_id', $id)->first();
    //     // $task->second_pdf_path = '';
    //     // $task->save();

    //     $delete_service = DB::table('table_task_second_service_files')->where('service_id', $id)->update([
    //         'status' => 0,
    //     ]);

    //     $task = DB::table('table_task_second_service_files')->where('service_id', $id)->first();

    //     // return redirect()->route('service-records.index')->with('success', 'Service Record deleted successfully');
    //     return redirect()->route('tasks.edit', $task->task_id)->with('success', 'Service Record deleted successfully');
    // }

    public function secondDestroy($id)
    {
        try {
            Log::info('SecondDestroy Method Called', ['service_id' => $id]);

            // Verify record exists
            $serviceFile = DB::table('table_task_second_service_files')
                ->where('service_id', $id)
                ->where('status', 1)
                ->first();

            if (!$serviceFile) {
                Log::warning('Second service file not found or already deleted', [
                    'service_id' => $id,
                    'query' => DB::table('table_task_second_service_files')->where('service_id', $id)->toSql()
                ]);
                return redirect()->back()->with('error', 'Service record not found or already deleted.');
            }

            // Update status to 0 (soft delete)
            $updated = DB::table('table_task_second_service_files')
                ->where('service_id', $id)
                ->update(['status' => 0]);

            if ($updated) {
                Log::info('Second service file soft deleted', [
                    'service_id' => $id,
                    'task_id' => $serviceFile->task_id
                ]);
                return redirect()->route('tasks.edit', $serviceFile->task_id)
                    ->with('success', 'Service Record deleted successfully');
            } else {
                Log::error('Failed to update second service file status', ['service_id' => $id]);
                return redirect()->back()->with('error', 'Failed to delete service record.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete second service record', [
                'service_id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to delete service record: ' . $e->getMessage());
        }
    }



    public function secondFromStore(Request $request)
{
    try {
        Log::info('Kompletter Request', $request->all());

        // Validation
        $validated = $request->validate([
            'auftraggeber' => 'string|nullable',
            'auftr_nr' => 'string|nullable',
            'kostenst' => 'string|nullable',
            'aart_der_ausgefuhrten' => 'string|nullable',
            'rsl' => 'string|nullable',
            'iso' => 'string|nullable',
            'iea' => 'string|nullable',
            'ansprechpartner' => 'string|nullable',
            'telefon' => 'string|nullable',
            'service_beleg_nr' => 'string|nullable',
            'ab_nr' => 'string|nullable',
            'rekla_sa_nr' => 'string|nullable',
            'debit_nr' => 'string|nullable',
            'reklamation' => 'boolean',
            'reparatur' => 'boolean',
            'rep_aufnahme' => 'boolean',
            'wartung' => 'boolean',
            'schulung' => 'boolean',
            'auslieferung' => 'boolean',
            'STK_bestanden_nein' => 'boolean',
            'funktiontest_besttanden_nein' => 'boolean',
            'bfk' => 'boolean',
            'kb' => 'boolean',
            'pb' => 'boolean',
            'nt' => 'boolean',
            'km' => 'boolean',
            'sonstiges' => 'boolean',
            'typ' => 'string|nullable',
            'serien_nr' => 'string|nullable',
            'funktion_in_ordnung' => 'boolean',
            'funktion_nicht_in_ordnung' => 'boolean',
            'bemerkungen' => 'string|nullable',
            'beschreibung' => 'string|nullable',
            'hotel_ubernachtung' => 'boolean',
            'hotel_von' => 'string|nullable',
            'hotel_bis' => 'string|nullable',
            'arbeit_fertig' => 'boolean',
            'funktiontest_besttanden_ja' => 'boolean',
            'kostenpflichtig_nein' => 'boolean',
            'kostenpflichtig' => 'boolean',
            'unter_vorbehalt' => 'boolean',
            'sign_date' => 'date|nullable',
            'techniker_name' => 'string|nullable',
            'kunde_unterschrift' => 'string|nullable',
            'arbeit_fertig_nein' => 'boolean',
            'STK_bestanden_ja' => 'boolean',
            'task_id' => 'required|integer|exists:tasks,id',
            'user_id' => 'integer|nullable',
            'customer_id' => 'integer|nullable',
            'notizen' => 'string|nullable',
            'servicebericht_both_beschreibung' => 'string|nullable',
            'datum_bis' => 'date|nullable',
            'datum_von' => 'date|nullable',
        ]);

        // Felder entfernen, die nicht direkt im ServiceRecord gespeichert werden
        $keysToUnset = [
            'materialconslieferung',
            'art_nr',
            'anfahrtzeit',
            'ruckfahrtzeit',
            'fahrt_km',
            'pausch_anfahrt',
            'wartezeit',
            'arbeitszeit',
            'ges_arbeitszeit',
            'personenzahl',
            'datum_material_tag',
            'datum_material_monat',
            'anfahrtzeit_von',
            'anfahrtzeit_bis',
            'anfahrtzeit_std',
            'ruckfahrtzeit_von',
            'ruckfahrtzeit_bis',
            'ruckfahrtzeit_std',
            'fahrt_km_hin',
            'fahrt_km_zurück',
            'ges_arbeitszeit_tag',
            'ges_arbeitszeit_monat',
            'Beschreibung',
        ];

        foreach ($keysToUnset as $key) {
            unset($validated[$key]);
        }

        // Signatur speichern
        if ($request->kunde_name) {
            $validated['kunde_name'] = $this->saveSignature($request->kunde_name, 'kunde_name');
        }

        // Service Record anlegen
        $serviceRecord = ServiceRecord::create($validated);

        // Materialverbrauch speichern
        foreach ($request->materialconslieferung_second ?? [] as $key => $material) {
            if (empty($material)) continue;

            DB::table('task_reports_material_consumption')->insert([
                'service_record_id' => $serviceRecord->id,
                'piece_stück' => $material,
                'Beschreibung' => $request->Beschreibung_second[$key] ?? '',
                'art_no_nr' => $request->art_no_nr_second[$key] ?? '',
            ]);
        }

        // Reisedetails speichern (neu, ohne datum_material_second)
        if (!empty($request->anfahrtzeit_von_second)) {
            foreach ($request->anfahrtzeit_von_second as $travelkey => $anfahrtVon) {
                Log::info('Speichere travel_detail:', [
                    'travelkey' => $travelkey,
                    'anfahrtzeit_von' => $anfahrtVon,
                    'ruckfahrtzeit_von' => $request->ruckfahrtzeit_von_second[$travelkey] ?? null,
                ]);

                DB::table('service_travel_details')->insert([
                    'service_record_id' => $serviceRecord->id,
                    'datum_material_tag' => $request->datum_material_tag_second[$travelkey] ?? null,
                    'datum_material_monat' => $request->datum_material_monat_second[$travelkey] ?? null,
                    'anfahrtzeit_von' => $anfahrtVon,
                    'anfahrtzeit_bis' => $request->anfahrtzeit_bis_second[$travelkey] ?? null,
                    'anfahrtzeit_std' => $request->anfahrtzeit_std_second[$travelkey] ?? null,
                    'ruckfahrtzeit_von' => $request->ruckfahrtzeit_von_second[$travelkey] ?? null,
                    'ruckfahrtzeit_bis' => $request->ruckfahrtzeit_bis_second[$travelkey] ?? null,
                    'ruckfahrtzeit_std' => $request->ruckfahrtzeit_std_second[$travelkey] ?? null,
                    'fahrt_km_hin' => $request->fahrt_km_hin_second[$travelkey] ?? null,
                    'fahrt_km_zurück' => $request->fahrt_km_zurück_second[$travelkey] ?? null,
                    'pausch_anfahrt' => $request->pausch_anfahrt_second[$travelkey] ?? null,
                    'wartezeit' => $request->wartezeit_second[$travelkey] ?? null,
                    'ges_arbeitszeit' => $request->ges_arbeitszeit_second[$travelkey] ?? null,
                    'ges_arbeitszeit_tag' => $request->ges_arbeitszeit_tag_second[$travelkey] ?? null,
                    'ges_arbeitszeit_monat' => $request->ges_arbeitszeit_monat_second[$travelkey] ?? null,
                    'personenzahl' => $request->personenzahl_second[$travelkey] ?? 1,
                ]);
            }
        }

        // PDF-Daten vorbereiten
        $task = Task::find($request->task_id);
        $user = User::find($request->user_id);
        $customer = Customer::find($request->customer_id);
        $service_material_consumption = DB::table('task_reports_material_consumption')
            ->where('service_record_id', $serviceRecord->id)
            ->get();
        $service_travel_details = DB::table('service_travel_details')
            ->where('service_record_id', $serviceRecord->id)
            ->get();

        // PDF generieren
        $pdf = Pdf::loadView('tasks.pdf', [
            'modal_title' => $task->title ?? '',
            'modal_description' => $task->comment ?? '',
            'user' => $user?->name ?? '',
            'customer' => $customer,
            'service' => $serviceRecord,
            'service_material_consumption' => $service_material_consumption,
            'service_travel_details' => $service_travel_details,
            'notizen' => $serviceRecord->notizen,
        ]);

        // PDF speichern
        $fileName = 'task_' . time() . '.pdf';
        $pdfPath = 'second_pdfs/' . $fileName;

        try {
            $pdf->save(public_path($pdfPath));
            Log::info('PDF Saved', ['path' => $pdfPath]);
        } catch (\Exception $e) {
            Log::error('Failed to save PDF', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to save PDF'], 500);
        }

        // PDF Pfad speichern
        DB::table('table_task_second_service_files')->insert([
            'task_id' => $request->task_id,
            'service_id' => $serviceRecord->id,
            'second_service_file' => $pdfPath,
        ]);

        return response()->json([
            'message' => 'Service record created successfully.',
            'flag' => 'add',
            'data' => $serviceRecord,
            'service_id' => $serviceRecord->id,
            'service_file' => $pdfPath,
        ]);

    } catch (\Exception $e) {
        Log::error('Second Service Record Creation Failed', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'error' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}




    
}

