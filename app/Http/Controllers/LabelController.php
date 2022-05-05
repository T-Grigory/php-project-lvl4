<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    private const EMPTY_LIST_OF_LABEL = 0;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $labels = Label::Orderby('id')->paginate(15);

        return view('label.index', compact('labels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Label::class);

        $label = new Label();
        return view('label.create', compact('label'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Label::class);

        $data = $this->validate($request, [
            'name' => 'required|unique:labels',
            'description' => 'nullable'
        ]);

        $label = new Label();
        $label->fill($data);
        $label->save();

        flash(__('flash.success.create', ['entity' => 'Метка', 'create' => 'создана']))->success();

        return redirect()->route('labels.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Label $label
     * @return \Illuminate\Http\Response
     */
    public function edit(Label $label)
    {
        $this->authorize('update', $label);

        return view('label.edit', compact('label'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Label $label
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Label $label)
    {
        $this->authorize('update', $label);

        $data = $this->validate($request, [
            'name' => 'required|unique:labels,name,' . $label->id,
            'description' => 'nullable'
        ]);

        $label->fill($data);
        $label->save();

        flash(__('flash.success.change', ['entity' => 'Метка', 'change' => 'изменена']))->success();

        return redirect()->route('labels.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Label $label
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $label = Label::find($id);

        if (!$label) {
            return redirect()->route('labels.index');
        }

        $this->authorize('delete', $label);

        $messagePath = 'flash.error.delete';
        $flashMethod = 'error';
        $entity = 'метку';

        if ($label->tasks->count() === self::EMPTY_LIST_OF_LABEL) {
            $label->delete();
            $messagePath = 'flash.success.delete';
            $flashMethod = 'success';
            $entity = 'метка';
        }

        flash(__($messagePath, ['entity' => $entity, 'delete' => 'удалена']))->$flashMethod();

        return redirect()->route('labels.index');
    }
}
