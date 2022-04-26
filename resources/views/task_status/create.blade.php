@extends('layouts.app')

@section('content')
    <h1 class="mb-5">Создать статус</h1>
    {{ Form::model($taskStatus, ['route' => 'task_statuses.store']) }}
    @include('task_status.form')
    {{ Form::submit('Создать', ['class' => 'btn btn-primary mt-3']) }}
    {{ Form::close() }}
@endsection
