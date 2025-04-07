<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TaskRequest;
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', Auth::id())->get();
        return response()->json($tasks);
    }


    public function create()
    {
        //
    }


    public function store(TaskRequest $request)
    {
        $task = Task::create([
            'title' => $request->title,
            'status' => $request->status ?? 'pending',
            'user_id' => Auth::id(),
        ]);
        return Response()->json([
            "message" => "Task created successfully", 'task' => $task
        ])->setStatusCode(201);
        
    }
    public function show(Task $task)
    {
    
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($task);
      
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return $task;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
       
        if(Auth::id() != $task->user_id){
            return Response()->json([
                "message" => "You are not authorized to update this task",
            ])->setStatusCode(403);
        }
        $task->update($request->all());
        return Response()->json([
            "message" => "Task updated successfully",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if(Auth::id() != $task->user_id){
            return Response()->json([
                "message" => "You are not authorized to delete this task",
            ])->setStatusCode(403);
        }
        $task->delete();
        return Response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }


}
