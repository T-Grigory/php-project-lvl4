@extends('layouts.app')

@section('content')
    <h1 class="mb-5">Задачи</h1>
    <div class="d-flex mb-3">
        <div>
            <form method="GET" action="https://php-task-manager-ru.hexlet.app/tasks" accept-charset="UTF-8">
                <div class="row g-1">
                    <div class="col">
                        <select class="form-select me-2" name="filter[status_id]">
                            <option selected="selected" value="">Статус</option>
                            @foreach($tasks as $task)
                                <option value="{{$task->status->id}}">{{$task->status->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <select class="form-select me-2" name="filter[created_by_id]">
                            <option selected="selected" value="">Автор</option>
                            @foreach($tasks as $task)
                                <option value="{{$task->createdBy->id}}">{{$task->createdBy->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <select class="form-select me-2" name="filter[assigned_to_id]">
                            <option selected="selected" value="">Исполнитель</option>
                        </select>
                    </div>
                    <div class="col">
                        <input class="btn btn-outline-primary me-2" type="submit" value="Применить">
                    </div>
                </div>
            </form>
        </div>

        <div class="ms-auto">
            @can('create', App\Models\Task::class)
                <a href="{{route('tasks.create')}}" class="btn btn-primary ml-auto">
                    Создать задачу
                </a>
            @endcan
        </div>
    </div>
    <table class="table me-2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Статус</th>
                <th>Имя</th>
                <th>Автор</th>
                <th>Исполнитель</th>
                <th>Дата создания</th>
                @auth
                    <th>Действия</th>
                @endauth
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)

                <tr>
                    <td>{{$task->id}}</td>
                    <td>{{$task->status->name}}</td>
                    <td><a  class="text-decoration-none" href="{{route('tasks.show', $task)}}">{{$task->name}}</a></td>
                    <td>{{$task->createdBy->name}}</td>
                    <td>{{$task->assignedTo->name ?? ''}}</td>
                    <td>{{$task->created_at}}</td>

                    <td>
                        @can('delete', $task)
                            <a class="text-danger text-decoration-none justify-content-start" href="{{route('tasks.destroy', $task)}}" data-confirm="Вы уверены?" data-method="delete">Удалить</a>
                        @endcan
                        @can('update', $task)
                            <a class="text-decoration-none justify-content-end" href="{{route('tasks.edit', $task)}}">Изменить</a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{$tasks->links()}}
@endsection
