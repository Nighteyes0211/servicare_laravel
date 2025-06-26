<?php

use App\Http\Controllers\Auth\AuthenticatedSessionControllerN;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\ServiceRecordController;
use App\Http\Controllers\VacationController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Hier kannst du Web-Routen für deine Anwendung registrieren. Diese Routen
| werden durch die RouteServiceProvider geladen und erhalten die "web"
| Middleware-Gruppe.
|
*/
Route::get('/', function () {
    return view('auth.login');
});

// Startseite / Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');


Route::get('/service-records/second-create-modal', [ServiceRecordController::class, 'createSecondServiceModal']);

// Benutzerverwaltung (nur für Admins)
Route::middleware(['auth'])->group(function () {
    Route::get('/user-management', [UserManagementController::class, 'index'])->name('user_management.index');
    Route::get('/user-management/create', [UserManagementController::class, 'create'])->name('user_management.create'); // GET-Route für Benutzererstellung
    Route::post('/user-management', [UserManagementController::class, 'store'])->name('user_management.store'); // POST-Route zum Speichern
    Route::get('/user-management/{id}/edit', [UserManagementController::class, 'edit'])->name('user_management.edit');
    Route::put('/user-management', action: [UserManagementController::class, 'update'])->name('user_management.update');
    Route::delete('/user-management/{id}', [UserManagementController::class, 'destroy'])->name('user_management.destroy');
    Route::resource('user-management', UserManagementController::class);
    Route::resource('task_categories', TaskCategoryController::class);
    Route::resource('user_management', UserManagementController::class);
    Route::get('user_management/{id}', [UserManagementController::class, 'show'])->name('user_management.show');

});


Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');





Route::middleware(['auth'])->group(function () {
    // Kunden-Routen (Resource Controller)
    Route::resource('customers', CustomerController::class);
    Route::get('articles/import', [ArticleController::class, 'showImportForm'])->name('articles.import.form');
    Route::post('articles/import', [ArticleController::class, 'import'])->name('articles.import');
    Route::resource('articles', ArticleController::class);


    // Ansprechpartner-Routen
    Route::prefix('customers/{customer}/contacts')->group(function () {
        Route::get('/create', [ContactController::class, 'create'])->name('contacts.create'); // Ansprechpartner hinzufügen (Formular anzeigen)
        Route::post('/', [ContactController::class, 'store'])->name('contacts.store'); // Ansprechpartner speichern
        Route::get('/{contact}', [ContactController::class, 'show'])->name('contacts.show'); // Ansprechpartner anzeigen (optional)
        Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit'); // Ansprechpartner bearbeiten (Formular anzeigen)
        Route::put('/{contact}', [ContactController::class, 'update'])->name('contacts.update'); // Ansprechpartner aktualisieren
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy'); // Ansprechpartner löschen
    });

    // Give a specific Customer Record
    Route::get('/customer-data/{id}', [CustomerController::class, 'CustomerData'])->name('customer-record.data');
});



Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');



// Kundenverwaltung (CRUD-Routen)
Route::middleware('auth')->group(function () {
    Route::resource('customers', CustomerController::class);
});

// Diagnosebögen (CRUD-Routen)
Route::middleware('auth')->group(function () {
    Route::resource('diagnoses', DiagnosisController::class);
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name(name: 'profile.destroy');

});


// Planner Filter Ajax
Route::middleware(['auth'])->group(function () {
    Route::get('/tasks/filter-by-role', [TaskController::class, 'filterUsersByRole'])->name('tasks.filterUsersByRole');

});




// Aufgabenverwaltung (CRUD-Routen)
Route::middleware('auth', )->group(function () {
        
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::get('/planner', [TaskController::class, 'planner'])->name('tasks.planner');

    Route::get('/tasks/json', [TaskController::class, 'getTasks'])->name('tasks.json');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');


Route::get('/tasks/unassigned', [TaskController::class, 'unassignedTasks'])->name('tasks.unassigned');



Route::post('/tasks/{id}/assign', [TaskController::class, 'assign'])->name('tasks.assign');


// Dann show-Route manuell mit Slash definieren



Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
Route::get('/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');




    Route::get('/fetch-articles', [TaskController::class, 'fetchArticles'])->name('articles.fetch');

    // Display the form to create a new service record
    Route::get('/service-records/create', [ServiceRecordController::class, 'create'])->name('service-records.create');
    // Store the service record data
    Route::post('/service-records/store', [ServiceRecordController::class, 'store'])->name('service-records.store');
    // Display a list of service records
    Route::get('/service-records', [ServiceRecordController::class, 'index'])->name('service-records.index');
    // Show a specific service record
    Route::get('/service-records/{id}', [ServiceRecordController::class, 'show'])->name('service-records.show');
    // Edit a service record
    Route::get('/service-records/{id}/edit', [ServiceRecordController::class, 'edit'])->name('service-records.edit');
    // Edit a service record
    Route::get('/service-records-second/{id}/edit', [ServiceRecordController::class, 'editSecondForm'])->name('service-records-second.edit');
    // Update a service record
    Route::put('/service-records/{id}', [ServiceRecordController::class, 'update'])->name('service-records.update');
    // Delete a service record
    Route::delete('/service-records/{id}', [ServiceRecordController::class, 'destroy'])->name('service-records.destroy');

    Route::post('/second-service-records/{id}', [ServiceRecordController::class, 'secondFromUpdate'])->name('second-service-records.update');
    
    Route::get('/second-service-records-delete-pdf/{id}', [ServiceRecordController::class, 'secondDestroy'])->name('second-service-records.destroy');

    //add Modal
    Route::get('/add-service-records', [ServiceRecordController::class, 'createServiceModal'])->name('service-records.add.modal');
    Route::get('/add-second-service-records', [ServiceRecordController::class, 'createSecondServiceModal'])->name('second-service-records.add.modal');
    Route::post('/second-service-records', [ServiceRecordController::class, 'secondFromStore'])->name('second-service-records.store');


    // Display a list of service records
    Route::get('/vacations', [VacationController::class, 'index'])->name('vacation.index');
    Route::get('/create-vacations', [VacationController::class, 'create'])->name('vacation.create');
    Route::post('/store-vacations', [VacationController::class, 'store'])->name('vacation.store');
    Route::get('/edit-vacations/{id}', [VacationController::class, 'edit'])->name('vacation.edit');
    Route::post('/update-vacations', [VacationController::class, 'update'])->name('vacation.update');
    Route::get('/show-vacations/{id}', [VacationController::class, 'show'])->name('vacation.show');
    Route::get('/destroy-vacations/{id}', [VacationController::class, 'destroy'])->name('vacation.destroy');
    Route::post('/action-vacations', [VacationController::class, 'actionUpdate'])->name('vacation.updateaction');
    Route::get('/pending-count', [VacationController::class, 'pendingApplicationCount'])->name('vacation.pendingcount');
    Route::get('/show-admin-vacations/{id}', [VacationController::class, 'showAdmin'])->name('vacationAdmin.show');
    Route::get('/show-user-vacations/{id}', [VacationController::class, 'showAllVacationAdmin'])->name('vacationAdmin.showUserVacation');


});


Route::middleware(['auth'])->group(function () {
    Route::get('/customers/{customer}/contacts', [CustomerController::class, 'contacts'])->name('customers.contacts');
});



// Logout-Route, falls nicht bereits in Auth::routes() enthalten
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login'); 
})->name('logout');



// Route::get('/login', [AuthenticatedSessionControllerN::class, 'create'])
//     ->middleware('guest')
//     ->name('login');

// Route::post('/login', [AuthenticatedSessionControllerN::class, 'store'])
//     ->middleware('guest');

require __DIR__.'/auth.php';


Route::get('/debug-routes', function () {
    return collect(\Route::getRoutes())->filter(function ($route) {
        return str_contains($route->uri(), 'tasks');
    })->map(function ($r) {
        return ['uri' => $r->uri(), 'name' => $r->getName(), 'action' => $r->getActionName()];
    });
});