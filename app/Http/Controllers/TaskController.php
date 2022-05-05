<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\LabelTask;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::Orderby('id')->paginate(15);

        return view('task.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', TaskStatus::class);

        $task = new Task();
        $users = User::all();
        $statuses = TaskStatus::all();
        $labels = Label::all();

        return view('task.create', compact('task', 'users', 'statuses', 'labels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', TaskStatus::class);

        $data = $this->validate($request, [
            'name' => 'required|unique:tasks',
            'description' => 'nullable',
            'status_id' => 'required',
            'assigned_to_id' => 'nullable',
            'labels' => 'array',
        ]);
        $labels = Arr::whereNotNull($data['labels'] ?? []);

        $task = new Task();

        $task->fill($data);
        $task->created_by_id = Auth::id();
        $task->save();

        $task->labels()->attach($labels);

        flash(__('flash.success.create', ['entity' => 'Задача', 'create' => 'создана']))->success();

        return redirect()->route('tasks.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return view('task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $users = User::all();
        $statuses = TaskStatus::all();
        $labels = Label::all();

        return view('task.edit', compact('task', 'users', 'statuses', 'labels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {

        $this->authorize('update', $task);

        $data = $this->validate($request, [
            'name' => 'required|unique:tasks,name,' . $task->id,
            'description' => 'nullable',
            'status_id' => 'required',
            'assigned_to_id' => 'nullable',
            'labels' => 'array',
        ]);

        $labels = Arr::whereNotNull($data['labels'] ?? []);

        $task->fill($data);
        $task->created_by_id = Auth::id();
        $task->save();

        $task->labels()->sync($labels);

        flash(__('flash.success.change', ['entity' => 'Задача', 'change' => 'изменена']))->success();

        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return redirect()->route('tasks.index');
        }

        $this->authorize('delete', $task);

        $task->labels()->detach();

        $task->delete();

        flash(__('flash.success.delete', ['entity' => 'Задача', 'delete' => 'удалена']))->success();

        return redirect()->route('tasks.index');
    }
}
