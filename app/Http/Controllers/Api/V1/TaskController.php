<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTaskRequest;
use App\Http\Requests\Api\V1\UpdateTaskRequest;
use App\Http\Requests\Api\V1\ReplaceTaskRequest;
use App\Http\Resources\V1\TaskResource;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;

class TaskController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->include('creator')) {
            return TaskResource::collection(Task::with('user')->paginate());
        }
        return TaskResource::collection(Task::paginate());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        try {
            $user = User::findOrFail($request->input('data.relationships.creator.data.id'));
        } catch (ModelNotFoundException $e) {
            return $this->ok('User not found', [
                'error' => 'The provided user ID does not exist'
            ]);
        }

        $model = [
            'user_id' => $request->input('data.relationships.creator.data.id'),
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
        ];
        return new TaskResource(Task::create($model));
    }

    /**
     * Display the specified resource.
     */
    public function show($task_id)
    {
        try {
            $task = Task::findOrFail($task_id);

            if ($this->include('creator')) {
                return new TaskResource($task->load('user'));
            }
            return new TaskResource($task);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Task cannot be found', 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        //
    }

    public function replace(ReplaceLoanRequest $request, $task_id)
    {
        try {
            $task = Task::findOrFail($task_id);

            $task->title = $request->input('data.attributes.title');
            $task->description = $request->input('data.attributes.description');
            $task->status = $request->input('data.attributes.status');

            $task->update();

            if ($this->include('creator')) {
                return new TaskResource($task->load('user'));
            }
            return new TaskResource($loan);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Task cannot be found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $task->delete();

            return $this->ok('Task deleted');
        } catch (ModelNotFoundException $exception) {
            return $this->error('Task not found.', 404);
        }
    }
}
