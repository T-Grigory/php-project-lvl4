<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskStatus;

class TaskStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
         $taskStatuses = TaskStatus::orderBy('id')->get();

        return view('task_status.index', compact('taskStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->authorize('create', TaskStatus::class);

        $taskStatus = new TaskStatus();

        return view('task_status.create', compact('taskStatus'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', TaskStatus::class);

        $data = $this->validate($request, [
           'name' => 'required|unique:task_statuses'
        ]);

        $taskStatus = new TaskStatus();

        $taskStatus->fill($data);
        $taskStatus->save();

        flash(__('flash.success.create'))->success();

        return redirect()->route('task_statuses.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $taskStatus = TaskStatus::findOrFail($id);

        $this->authorize('update', $taskStatus);

        return view('task_status.edit', compact('taskStatus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $taskStatus = TaskStatus::findOrFail($id);

        $this->authorize('update', $taskStatus);

        $data = $this->validate($request, [
           'name' => 'required|unique:task_statuses,name,' . $taskStatus->id,
        ]);

        $taskStatus->fill($data);
        $taskStatus->save();

        flash(__('flash.success.change'))->success();

        return redirect()->route('task_statuses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $taskStatus = TaskStatus::find($id);

        $this->authorize('delete', $taskStatus);

        if ($taskStatus) {
            $taskStatus->delete();
        }

        flash(__('flash.success.delete'))->success();

        return redirect()->route('task_statuses.index');
    }
}
