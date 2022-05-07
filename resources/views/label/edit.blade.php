@extends('layouts.app')


@section('content')
    <h1 class="mb-5">Изменение метки</h1>
    {{ Form::model($label, ['url' => route('labels.update', $label), 'method' => 'PATCH']) }}
    @include('label.form')
    {{ Form::submit('Обновить', ['class' => 'btn btn-primary mt-3']) }}
    {{ Form::close() }}
@endsection
