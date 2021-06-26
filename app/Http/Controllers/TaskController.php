<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Task::whereUserId(auth()->user()->id)->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TaskRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $task = Task::create(array_merge($validated, ['user_id' => auth()->user()->id]));
        return response()->json([
            'message' => 'Task inserted successfully.',
            'task' => $task
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        if (! Gate::allows('check-task', $task)) {
            abort(404);
        }

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TaskRequest $request
     * @param  Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskRequest $request, Task $task)
    {
        if (! Gate::allows('check-task', $task)) {
            abort(404);
        }

        $task->update($request->only(['title','description']));

        return response()->json([
            'message' => 'Task update successfully.',
            'task' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
        if (! Gate::allows('check-task', $task)) {
            abort(404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }
}
