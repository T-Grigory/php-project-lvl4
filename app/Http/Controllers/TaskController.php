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
    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    public function index(): View
    {
        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters([
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('created_by_id'),
                AllowedFilter::exact('assigned_to_id')
            ])
            ->orderBy('id')
            ->paginate();

        $users = User::all();
        $statuses = TaskStatus::all();
        $query = request()->input('filter') ?? [];

        return view('task.index', compact('tasks', 'users', 'statuses', 'query'));
    }

    public function create(): View
    {
        $task = new Task();
        $users = User::all();
        $statuses = TaskStatus::all();
        $labels = Label::all();

        return view('task.create', compact('task', 'users', 'statuses', 'labels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request, [
            'name' => 'required|max:255|unique:tasks',
            'description' => 'nullable',
            'status_id' => 'required',
            'assigned_to_id' => 'nullable',
            'labels' => 'array',
        ]);
        $labels = Arr::whereNotNull($data['labels'] ?? []);

        $task = new Task(['created_by_id' => Auth::id()]);
        $task->fill($data);
        $task->save();

        $task->labels()->attach($labels);

        flash(__('flash.task.store.success'))->success();

        return redirect()->route('tasks.index');
    }

    public function show(Task $task): View
    {
        return view('task.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        $users = User::all();
        $statuses = TaskStatus::all();
        $labels = Label::all();

        return view('task.edit', compact('task', 'users', 'statuses', 'labels'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $data = $this->validate($request, [
            'name' => 'required|max:255|unique:tasks,name,' . $task->id,
            'description' => 'nullable',
            'status_id' => 'required',
            'assigned_to_id' => 'nullable',
            'labels' => 'array',
        ]);

        $labels = Arr::whereNotNull($data['labels'] ?? []);

        $task->fill($data);
        $task->save();

        $task->labels()->sync($labels);

        flash(__('flash.task.update.success'))->success();

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->labels()->detach();

        $task->delete();

        flash(__('flash.task.destroy.success'))->success();

        return redirect()->route('tasks.index');
    }
}
