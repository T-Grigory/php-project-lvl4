<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    public function index(): View
    {

        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters([
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('created_by_id'),
                AllowedFilter::exact('assigned_to_id')
            ])
            ->orderBy('id')
            ->paginate(15);


        $users = User::all();
        $statuses = TaskStatus::all();

        $query = request()->query->all();

        $filter = [
            'statusID' => $query['filter']['status_id'] ?? null,
            'createdByID' => $query['filter']['created_by_id'] ??  null,
            'assignedToID' => $query['filter']['assigned_to_id'] ??  null,
        ];

        return view('task.index', compact('tasks', 'users', 'statuses'), $filter);
    }

    public function create(): View
    {
        $this->authorize('create', TaskStatus::class);

        $task = new Task();
        $users = User::all();
        $statuses = TaskStatus::all();
        $labels = Label::all();

        return view('task.create', compact('task', 'users', 'statuses', 'labels'));
    }

    public function store(Request $request): RedirectResponse
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

        flash(__('flash.success.feminine.create', ['entity' => 'задача']))->success();

        return redirect()->route('tasks.index');
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        return view('task.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        $users = User::all();
        $statuses = TaskStatus::all();
        $labels = Label::all();

        return view('task.edit', compact('task', 'users', 'statuses', 'labels'));
    }

    public function update(Request $request, Task $task): RedirectResponse
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
        $task->save();

        $task->labels()->sync($labels);

        flash(__('flash.success.feminine.change', ['entity' => 'задача']))->success();

        return redirect()->route('tasks.index');
    }

    public function destroy($id): RedirectResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return redirect()->route('tasks.index');
        }

        $this->authorize('delete', $task);

        $task->labels()->detach();

        $task->delete();

        flash(__('flash.success.feminine.delete', ['entity' => 'задача']))->success();

        return redirect()->route('tasks.index');
    }
}
