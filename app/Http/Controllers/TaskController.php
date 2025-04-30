<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::all();
        $tasks = Task::when($request->project_id, function ($query) use ($request) {
            $query->where('project_id', $request->project_id);
        })->orderBy('priority')->get();

        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        $priority = Task::max('priority') + 1;

        Task::create([
            'name' => $request->name,
            'priority' => $priority,
            'project_id' => $request->project_id,
        ]);

        return back();
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->only(['name', 'project_id']));
        return back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $index => $id) {
            Task::where('id', $id)->update(['priority' => $index + 1]);
        }

        return response()->json(['status' => 'success']);
    }
}