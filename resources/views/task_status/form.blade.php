<div class="form-group mb-3">
    <label for="name">Имя</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $taskStatus->name) }}">
    @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ __($message, ['entity' => 'статус']) }}</strong>
        </span>
    @enderror
</div>

