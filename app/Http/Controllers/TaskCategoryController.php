<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    /**
     * Zeigt eine Liste aller Aufgabenkategorien.
     */
    public function index()
    {
        $categories = TaskCategory::all();
        return view('task_categories.index', compact('categories'));
    }

    /**
     * Zeigt das Formular zum Erstellen einer neuen Kategorie.
     */
    public function create()
    {
        return view('task_categories.create');
    }

    /**
     * Speichert eine neue Aufgabenkategorie.
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:task_categories,name',
        'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validierung für das Icon
        'color' => 'nullable|string|size:7', // neu: HEX Farbe wie "#FF5733"
    ]);

    $iconPath = null;
    if ($request->hasFile('icon')) {
        $iconPath = $request->file('icon')->store('icons', 'public'); // Speichert das Icon im Ordner `storage/app/public/icons`
    }

    TaskCategory::create([
        'name' => $validated['name'],
        'icon' => $iconPath,
        'color' => $validated['color'] ?? null,

    ]);

    return redirect()->route('task_categories.index')->with('success', 'Kategorie erfolgreich erstellt.');
}

    /**
     * Zeigt das Formular zum Bearbeiten einer bestehenden Kategorie.
     */
    public function edit(TaskCategory $taskCategory)
    {
        return view('task_categories.edit', compact('taskCategory'));
    }

    /**
     * Aktualisiert eine bestehende Aufgabenkategorie.
     */
    public function update(Request $request, TaskCategory $taskCategory)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:task_categories,name,' . $taskCategory->id,
        'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validierung für das Icon
        'color' => 'nullable|string|size:7', // neu: HEX Farbe wie "#FF5733"
    ]);

    if ($request->hasFile('icon')) {
        // Lösche das alte Icon, falls vorhanden
        if ($taskCategory->icon) {
            \Storage::disk('public')->delete($taskCategory->icon);
        }

        $iconPath = $request->file('icon')->store('icons', 'public');
        $taskCategory->icon = $iconPath;
    }

    $taskCategory->name = $validated['name'];
    $taskCategory->color = $validated['color'] ?? $taskCategory->color;

    $taskCategory->save();

    return redirect()->route('task_categories.index')->with('success', 'Kategorie erfolgreich aktualisiert.');
}

    /**
     * Löscht eine Aufgabenkategorie.
     */
    public function destroy(TaskCategory $taskCategory)
{
    // Schutz für wichtige Kategorien
    if (in_array($taskCategory->name, ['Montage', 'Urlaub', 'Krank'])) {
        return redirect()->route('task_categories.index')
                         ->with('error', 'Diese Kategorie kann nicht gelöscht werden.');
    }

    $taskCategory->delete();

    return redirect()->route('task_categories.index')
                     ->with('success', 'Kategorie erfolgreich gelöscht.');
}

}
