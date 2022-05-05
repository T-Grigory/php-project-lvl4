@extends('layouts.app')

@section('content')
    <h1 class="mb-5">Просмотр задачи: {{$task->name}} <a href="{{route('tasks.edit', $task)}}">⚙</a></h1>
    <p>Имя: {{$task->name}}</p>
    <p>Статус: {{$task->status->name}}</p>
    <p>Описание: {{$task->description}}</p>
    @if ($task->labels->count() > 0)
        <p>Метки:</p>
        <ul>
            @foreach($task->labels as $label)
                <li>{{$label->name}}</li>
            @endforeach
        </ul>
    @endif


@endsection
