@extends('layouts.app')

@section('content')
    <h1 class="mb-5">Создать задачу</h1>
    {{ Form::model($task, ['route' => 'tasks.store', 'class' => 'w-50']) }}
    @include('task.form')
    {{ Form::submit('Создать', ['class' => 'btn btn-primary mt-3']) }}
    {{ Form::close() }}
@endsection
