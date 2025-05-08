<?php

namespace App\Http\Controllers;
use App\Models\Task;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('tasks', compact('tasks'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // Check for duplicates
        if (Task::where('title', $request->title)->exists()) {
            return response()->json([
                'error' => 'Task already exists!'
            ], 422);
        }

        // Create new task
        $task = Task::create([
            'title' => $request->title,
            'completed' => false
        ]);

        return response()->json([
            'task' => $task,
            'success' => true,
            'message' => 'Task created successfully'
        ]);
    }
    public function toggleComplete(Request $request, Task $task)
    {
        $task->completed = !$task->completed;
        $task->save();

        return response()->json([
            'success' => true,
            'completed' => $task->completed
        ]);
    }
    public function destroy(Task $task)
    {
        $task->delete();
        
        return response()->json([
            'success' => true
        ]);
    }

}
