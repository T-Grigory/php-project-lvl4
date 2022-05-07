<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LabelController extends Controller
{
    private const EMPTY_LIST_OF_LABEL = 0;

    public function index(): View
    {
        $labels = Label::Orderby('id')->paginate(15);

        return view('label.index', compact('labels'));
    }

    public function create(): View
    {
        $this->authorize('create', Label::class);

        $label = new Label();

        return view('label.create', compact('label'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Label::class);

        $data = $this->validate($request, [
            'name' => 'required|unique:labels',
            'description' => 'nullable'
        ]);

        $label = new Label();
        $label->fill($data);
        $label->save();

        flash(__('flash.success.feminine.create', ['entity' => 'метка']))->success();

        return redirect()->route('labels.index');
    }

    public function edit(Label $label): View
    {
        $this->authorize('update', $label);

        return view('label.edit', compact('label'));
    }

    public function update(Request $request, Label $label): RedirectResponse
    {
        $this->authorize('update', $label);

        $data = $this->validate($request, [
            'name' => 'required|unique:labels,name,' . $label->id,
            'description' => 'nullable'
        ]);

        $label->fill($data);
        $label->save();

        flash(__('flash.success.feminine.change', ['entity' => 'метка']))->success();

        return redirect()->route('labels.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $label = Label::find($id);

        if (is_null($label)) {
            return redirect()->route('labels.index');
        }

        $this->authorize('delete', $label);

        if ($label->tasks->count() === self::EMPTY_LIST_OF_LABEL) {
            $label->delete();
            flash(__('flash.success.feminine.delete', ['entity' => 'метка']))->success();
        } else {
            flash(__('flash.error.delete', ['entity' => 'метку']))->error();
        }

        return redirect()->route('labels.index');
    }
}
