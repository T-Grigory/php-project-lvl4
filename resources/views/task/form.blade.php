<div class="form-group mb-3">
    <label for="name">Имя</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') ?? $task->name }}">
    @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ __($message, ['entity' => 'задача']) }}</strong>
        </span>
    @enderror
</div>
<div class="form-group mb-3">
    <label for="description">Описание</label>
    <textarea class="form-control @error('description') is-invalid @enderror" cols="50" rows="10" name="description" id="description">{{old('description') ?? $task->description}}</textarea>
    @error('description')
        <span class="invalid-feedback" role="alert">
            <strong>{{ __($message) }}</strong>
        </span>
    @enderror
</div>
<div class="form-group mb-3">
    <label for="status_id">Статус</label>
    <select class="form-control @error('status_id') is-invalid @enderror" name="status_id" id="status_id">
        <option selected value>----------</option>
        @foreach($statuses as $status)
            <option value="{{$status->id}}" @if($status->id === $task->status_id) selected @endif>{{$status->name}}</option>
        @endforeach
    </select>

    @error('status_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ __($message) }}</strong>
        </span>
    @enderror
</div>
<div class="form-group mb-3">
    <label for="assigned_to_id">Исполнитель</label>
    <select class="form-control @error('assigned_to_id') is-invalid @enderror" name="assigned_to_id" id="assigned_to_id">
        <option selected value>----------</option>
        @foreach($users as $user)
            <option value="{{$user->id}}" @if($user->id === $task->assigned_to_id) selected @endif>{{$user->name}}</option>
        @endforeach
    </select>

    @error('assigned_to_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ __($message) }}</strong>
        </span>
    @enderror
</div>
<div class="form-group mb-3">
    <label for="labels">Метки</label>
    <select class="form-control" multiple="" name="labels[]">
        <option value=""></option>
        @foreach($labels as $label)
            <option value="{{$label->id}}" @if($task->labels->contains($label)) selected @endif>{{$label->name}}</option>
        @endforeach
    </select>
</div>
