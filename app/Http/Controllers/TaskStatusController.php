<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TaskStatusController extends Controller
{
    public function index(): View
    {
        $taskStatuses = TaskStatus::orderBy('id')->paginate(15);

        return view('task_status.index', compact('taskStatuses'));
    }

    public function create(): View
    {
        $this->authorize('create', TaskStatus::class);

        $taskStatus = new TaskStatus();

        return view('task_status.create', compact('taskStatus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', TaskStatus::class);

        $data = $this->validate($request, [
           'name' => 'required|max:255|unique:task_statuses'
        ]);

        $taskStatus = new TaskStatus();

        $taskStatus->fill($data);
        $taskStatus->save();

        flash(__('flash.success.masculine.create', ['entity' => 'статус']))->success();

        return redirect()->route('task_statuses.index');
    }

    public function edit(TaskStatus $taskStatus): View
    {
        $this->authorize('update', $taskStatus);

        return view('task_status.edit', compact('taskStatus'));
    }


    public function update(Request $request, TaskStatus $taskStatus): RedirectResponse
    {

        $this->authorize('update', $taskStatus);

        $data = $this->validate($request, [
           'name' => 'required|max:255|unique:task_statuses,name,' . $taskStatus->id,
        ]);

        $taskStatus->fill($data);
        $taskStatus->save();

        flash(__('flash.success.masculine.change', ['entity' => 'статус']))->success();

        return redirect()->route('task_statuses.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $taskStatus = TaskStatus::find($id);

        if (is_null($taskStatus)) {
            return redirect()->route('task_statuses.index');
        }
        $this->authorize('delete', $taskStatus);

        if (!$taskStatus->tasks()->exists()) {
            $taskStatus->delete();
            flash(__('flash.success.masculine.delete', ['entity' => 'статус']))->success();
        } else {
            flash(__('flash.error.delete', ['entity' => 'статус']))->error();
        }

        return redirect()->route('task_statuses.index');
    }
}
