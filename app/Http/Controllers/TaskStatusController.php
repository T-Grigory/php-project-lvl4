<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TaskStatusController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TaskStatus::class);
    }
    public function index(): View
    {
        $taskStatuses = TaskStatus::orderBy('id')->paginate();

        return view('task_status.index', compact('taskStatuses'));
    }

    public function create(): View
    {
        $taskStatus = new TaskStatus();

        return view('task_status.create', compact('taskStatus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request, [
           'name' => 'required|max:255|unique:task_statuses'
        ]);

        $taskStatus = new TaskStatus();

        $taskStatus->fill($data);
        $taskStatus->save();

        flash(__('flash.task_status.store.success'))->success();

        return redirect()->route('task_statuses.index');
    }

    public function edit(TaskStatus $taskStatus): View
    {
        return view('task_status.edit', compact('taskStatus'));
    }


    public function update(Request $request, TaskStatus $taskStatus): RedirectResponse
    {
        $data = $this->validate($request, [
           'name' => 'required|max:255|unique:task_statuses,name,' . $taskStatus->id,
        ]);

        $taskStatus->fill($data);
        $taskStatus->save();

        flash(__('flash.task_status.update.success'))->success();

        return redirect()->route('task_statuses.index');
    }

    public function destroy(TaskStatus $taskStatus): RedirectResponse
    {
        if (!$taskStatus->tasks()->exists()) {
            $taskStatus->delete();
            flash(__('flash.task_status.destroy.success'))->success();
        } else {
            flash(__('flash.task_status.destroy.error'))->error();
        }

        return redirect()->route('task_statuses.index');
    }
}
