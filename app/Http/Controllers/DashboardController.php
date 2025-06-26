<?php



namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\EmployeeVacation;
use App\Models\User;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        $tasks = Task::all();

        $doneCount = Task::where('status', 'done')->count();
        $notDoneCount = Task::where('status', 'not_done')->count();

        if(auth()->user()->isAdmin()) {
            $vacations = EmployeeVacation::orderBy('id', 'desc')->get();
        } else {
            $vacations = EmployeeVacation::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        }

        return view('dashboard.index', compact('tasks', 'doneCount', 'notDoneCount', 'vacations'));
    }   
}
