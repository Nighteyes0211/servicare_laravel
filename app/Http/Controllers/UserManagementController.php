<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeVacation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Zeigt die Liste aller Benutzer.
     */
    public function index()
    {
        $users = User::all(); // Alle Benutzer laden
        return view('user_management.index', compact('users'));
    }

    /**
     * Zeigt das Formular zum Erstellen eines neuen Benutzers.
     */
    public function create()
    {
        return view('user_management.create'); // Formular anzeigen
    }

    /**
     * Speichert einen neuen Benutzer in der Datenbank.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:admin,employee',
        'vacation_days' => 'required|integer|min:0',
    ]);

    $validated['password'] = Hash::make($validated['password']);
    $validated['four_day_week'] = $request->boolean('four_day_week');

    User::create($validated);

    return redirect()->route('user_management.index')->with('success', 'Benutzer erfolgreich hinzugefügt');
}


    public function show($id)
    {
        $user = User::find($id);
        $user_vacation = EmployeeVacation::where('user_id', $user->id)->get();
        // dd($user, $user_vacation);
        // return view('user_management.showUserVacation',compact('user_vacation', 'user'));
        return view('user_management.show', compact('user_vacation', 'user'));
    }

    /**
     * Zeigt das Formular zum Bearbeiten eines Benutzers.
     */
    public function edit($user)
    {
        $user = User::where('id', $user)->first();
        return view('user_management.edit', compact('user')); // Formular anzeigen
    }

    /**
     * Aktualisiert die Informationen eines Benutzers.
     */
    public function update(Request $request, $id)
    {
        // Find user by ID
        $user = User::findOrFail($id);
    
        // Validate the input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,employee',
            'vacation_days' => 'required|integer|min:0',
        ]);
    
        // Update the user data
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'vacation_days' => $validated['vacation_days'],
            'four_day_week' => $request->boolean('four_day_week'),
        ]);
    
        // If password is provided, update it
        if (!empty($validated['password'])) {
            $hashedPassword = Hash::make($validated['password']);
            $user->update(['password' => $hashedPassword]);
        }


        $user->four_day_week = $request->boolean('four_day_week');

    
        // Redirect to user management index with success message
        return redirect()->route('user_management.index')->with('success', 'Benutzer erfolgreich aktualisiert');
    }







    /**
     * Löscht einen Benutzer aus der Datenbank.
     */
    public function destroy($id)
    {
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('user_management.index')->with('error', 'User not found');
        }

        \Log::info('Deleting user with ID: ' . $user->id);  // Log the user ID

        // Delete the user
        $user->delete();

        return redirect()->route('user_management.index')->with('success', 'Benutzer erfolgreich gelöscht');
    }

}
