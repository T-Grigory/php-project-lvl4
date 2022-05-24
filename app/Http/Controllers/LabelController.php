<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LabelController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Label::class);
    }
    public function index(): View
    {
        $labels = Label::OrderBy('id')->paginate();
        return view('label.index', compact('labels'));
    }

    public function create(): View
    {
        $label = new Label();

        return view('label.create', compact('label'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request, [
            'name' => 'required|max:255|unique:labels',
            'description' => 'nullable'
        ]);

        $label = new Label();
        $label->fill($data);
        $label->save();

        flash(__('flash.label.store.success'))->success();

        return redirect()->route('labels.index');
    }

    public function edit(Label $label): View
    {
        return view('label.edit', compact('label'));
    }

    public function update(Request $request, Label $label): RedirectResponse
    {
        $data = $this->validate($request, [
            'name' => 'required|max:255|unique:labels,name,' . $label->id,
            'description' => 'nullable|max:255'
        ]);

        $label->fill($data);
        $label->save();

        flash(__('flash.label.update.success'))->success();

        return redirect()->route('labels.index');
    }

    public function destroy(Label $label): RedirectResponse
    {
        if (!$label->tasks()->exists()) {
            $label->delete();
            flash(__('flash.label.destroy.success'))->success();
        } else {
            flash(__('flash.label.destroy.error'))->error();
        }

        return redirect()->route('labels.index');
    }
}
