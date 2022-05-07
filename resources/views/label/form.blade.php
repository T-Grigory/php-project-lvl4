<div class="form-group mb-3">
    <label for="name">Имя</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $label->name) }}">
    @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ __($message, ['entity' => 'метка']) }}</strong>
        </span>
    @enderror
</div>
<div class="form-group mb-3">
    <label for="description">Описание</label>
    <textarea class="form-control" name="description" cols="50" rows="10" id="description">{{old('description', $label->description)}}</textarea>
</div>
