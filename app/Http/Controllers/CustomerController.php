<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Task;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // public function index()
    // {
    //     $customers = Customer::paginate(15);
    //     return view('customers.index', compact('customers'));
    // }

        public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->appends($request->only('search'));

        return view('customers.index', compact('customers'));
    }




    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {


        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Kunde erfolgreich erstellt.');
    }

    public function show(Customer $customer)
    {
        $tasks = Task::where('customer_id', $customer->id)->paginate(5);
        // dd($tasks);
        return view('customers.show', compact('customer', 'tasks'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

   public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Kunde erfolgreich aktualisiert.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Kunde erfolgreich gelöscht');
    }

    public function CustomerData($id) {
        $customer = Customer::find($id);
    
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
    
        return response()->json($customer);
    }


      public function contacts(Customer $customer)
{
    return response()->json(
        $customer->contacts()->select('id', 'salutation', 'first_name', 'last_name')->get()
    );
}

    
}
