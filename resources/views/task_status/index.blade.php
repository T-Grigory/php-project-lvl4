@extends('layouts.app')

@section('content')
    <h1 class="mb-5">Статусы</h1>
    @can('create', \App\Models\TaskStatus::class)
        <a href="{{route('task_statuses.create')}}" class="btn btn-primary">Создать статус</a>
    @endcan
    <table class="table mt-2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Дата создания</th>
                @auth
                    <th>Действия</th>
                @endauth
            </tr>
        </thead>
        <tbody>
            @foreach($taskStatuses as $status)
                <tr>
                    <td>{{$status->id}}</td>
                    <td>{{$status->name}}</td>
                    <td>{{$status->created_at->format('d.m.Y')}}</td>
                    <td>
                        @can('delete', $status)
                            <a href="{{route('task_statuses.destroy', $status)}}"  rel="nofollow" data-confirm="Вы уверены?" data-method="delete" class="text-danger text-decoration-none">
                                Удалить
                            </a>
                        @endcan
                        @can('update', $status)
                            <a class="text-decoration-none" href="{{route('task_statuses.edit', $status)}}">
                                Изменить
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{$taskStatuses->links()}}
@endsection
