@foreach ($items as $item)
    <option value="{{ $item->id }}">{{ $item->descripcion }}</option>
@endforeach
