@extends('layouts.app')

@section('content')
    <h1 class="mb-5">Изменить задачу</h1>
    {{ Form::model($task, ['url' => route('tasks.update', $task), 'class' => 'w-50', 'method' => 'PATCH']) }}
    @include('task.form')
    {{ Form::submit('Обновить', ['class' => 'btn btn-primary mt-3']) }}
    {{ Form::close() }}
@endsection
