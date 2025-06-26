<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Contact;

class ContactController extends Controller
{
    public function create(Customer $customer)
    {
        return view('contacts.create', compact('customer'));
    }

    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'salutation' => 'required|in:Herr,Frau',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        $customer->contacts()->create($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Ansprechpartner hinzugefügt.');
    }

    // 👇 HIER fügst du die destroy()-Methode ein:
    public function destroy(Customer $customer, Contact $contact)
    {
        // Sicherheitshalber prüfen, ob Kontakt wirklich zu Kunde gehört
        if ($contact->customer_id !== $customer->id) {
            abort(403, 'Dieser Kontakt gehört nicht zu diesem Kunden.');
        }

        $contact->delete();

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Ansprechpartner erfolgreich gelöscht.');
    }
}





