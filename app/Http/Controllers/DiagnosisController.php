<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Customer;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function index()
    {
        $diagnoses = Diagnosis::with('customer')->get();
        return view('diagnoses.index', compact('diagnoses'));
    }

    public function create()
    {
        $customers = Customer::get(); // Kundenliste zum Zuordnen des Diagnosebogens
        return view('diagnoses.create', compact('customers'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'type' => 'nullable|array',
            'type.*' => 'string|in:repair,complaint,confirmation,inquiry,quote,maintenance,note,order',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'customer_id' => 'required|integer|exists:customers,id',
            'action' => 'nullable|array',
            'action.*' => 'string|in:PB,PW,JT,DM,other',
            'forwarded_to' => 'nullable|string|max:255',
            'diagnosis_date' => 'required|date',
            'diagnosis_details' => 'required|string|max:5000',
        ]);

        // Convert `type` array to individual columns
        $types = [
            'repair' => in_array('repair', $request->type ?? []),
            'complaint' => in_array('complaint', $request->type ?? []),
            'confirmation' => in_array('confirmation', $request->type ?? []),
            'inquiry' => in_array('inquiry', $request->type ?? []),
            'quote' => in_array('quote', $request->type ?? []),
            'maintenance' => in_array('maintenance', $request->type ?? []),
            'note' => in_array('note', $request->type ?? []),
            'order' => in_array('order', $request->type ?? []),
        ];

        // Convert `action` array to individual columns
        $actions = [
            'pb' => in_array('PB', $request->action ?? []),
            'pw' => in_array('PW', $request->action ?? []),
            'jt' => in_array('JT', $request->action ?? []),
            'dm' => in_array('DM', $request->action ?? []),
            'notes' => in_array('other', $request->action ?? []),
        ];

        // Prepare data for saving
        $data = array_merge($types, $actions, [
            'name' => $request->name,
            'phone' => $request->phone,
            'customer_id' => $request->customer_id,
            'forwarded_to' => $request->forwarded_to,
            'diagnosis_date' => $request->diagnosis_date,
            'diagnosis_details' => $request->diagnosis_details,
            'address' => $request->address ?? null, // Optional
            'organization' => $request->organization ?? null, // Optional
            'action' => implode(',', $request->action ?? []),
            'type' => implode(', ', $request->type ?? [])
        ]);

        // Save the data in the database
        Diagnosis::create($data);


        return redirect()->route('diagnoses.index')->with('success', 'Diagnosebogen erfolgreich erstellt');
    }
    public function show(Diagnosis $diagnosis)
    {
        return view('diagnoses.show', compact('diagnosis'));
    }

    public function edit(Diagnosis $diagnosis)
    {
        $customers = Customer::all();
        return view('diagnoses.edit', compact('diagnosis', 'customers'));
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
        $request->validate([
            'type' => 'nullable|array',
            'type.*' => 'string|in:repair,complaint,confirmation,inquiry,quote,maintenance,note,order',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'customer_id' => 'required|integer|exists:customers,id',
            'action' => 'nullable|array',
            'action.*' => 'string|in:PB,PW,JT,DM,other',
            'forwarded_to' => 'nullable|string|max:255',
            'diagnosis_date' => 'required|date',
            'diagnosis_details' => 'required|string|max:5000',
        ]);

        // Convert `type` array to individual columns
        $types = [
            'repair' => in_array('repair', $request->type ?? []),
            'complaint' => in_array('complaint', $request->type ?? []),
            'confirmation' => in_array('confirmation', $request->type ?? []),
            'inquiry' => in_array('inquiry', $request->type ?? []),
            'quote' => in_array('quote', $request->type ?? []),
            'maintenance' => in_array('maintenance', $request->type ?? []),
            'note' => in_array('note', $request->type ?? []),
            'order' => in_array('order', $request->type ?? []),
        ];

        // Convert `action` array to individual columns
        $actions = [
            'pb' => in_array('PB', $request->action ?? []),
            'pw' => in_array('PW', $request->action ?? []),
            'jt' => in_array('JT', $request->action ?? []),
            'dm' => in_array('DM', $request->action ?? []),
            'notes' => in_array('other', $request->action ?? []),
        ];

        // Prepare data for updating
        $data = array_merge($types, $actions, [
            'name' => $request->name,
            'phone' => $request->phone,
            'customer_id' => $request->customer_id,
            'forwarded_to' => $request->forwarded_to,
            'diagnosis_date' => $request->diagnosis_date,
            'diagnosis_details' => $request->diagnosis_details,
            'address' => $request->address ?? null, // Optional
            'organization' => $request->organization ?? null, // Optional
            'action' => implode(',', $request->action ?? []),
            'type' => implode(', ', $request->type ?? [])
        ]);


        // Update the diagnosis in the database
        $diagnosis->update($data);

        return redirect()->route('diagnoses.index')->with('success', 'Diagnosebogen erfolgreich aktualisiert');
    }

    public function destroy(Diagnosis $diagnosis)
    {
        $diagnosis->delete();
        return redirect()->route('diagnoses.index')->with('success', 'Diagnosebogen erfolgreich gelöscht');
    }
}
