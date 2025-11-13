<ul class="list-group list-group-flush">
    @foreach ($subgroups as $sub)
        <li class="list-group-item">
            {{-- Formulario para agregar sub-subgrupo --}}
            <form action="{{ route('subgroups.store') }}" method="POST" class="mt-2 mb-3">
                @csrf
                <input type="hidden" name="group_id" value="{{ $sub->group_id }}">
                <input type="hidden" name="parent_subgroup_id" value="{{ $sub->id }}">
                <div class="form-row">
                    <div class="col form-group">
                        <input type="text" name="descripcion" class="form-control" placeholder="Descripción"
                            required>
                    </div>
                    <div class="col form-group">
                        <input type="text" name="abreviacion" class="form-control" placeholder="Abreviación"
                            required>
                    </div>

                </div>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Subgrupo
                </button>
            </form>

            <p>{{ $sub->descripcion }} ({{ $sub->abreviacion }})</p>

            {{-- Botones de acción --}}
            <div class="mt-1 mb-4">
                <a href="{{ route('subgroups.edit', $sub->id) }}" class="btn btn-sm btn-warning">
                    <i class="fa-solid fa-pen"></i> Editar
                </a>
                <form action="{{ route('subgroups.destroy', $sub->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('¿Eliminar este subgrupo?')">
                        <i class="fa-solid fa-trash"></i> Eliminar
                    </button>
                </form>
            </div>

            {{-- Sub-subgrupos --}}
            @if ($sub->subgroups->isNotEmpty())
                <h6>Subsubgrupos</h6>
                @include('subgroups.tree', ['subgroups' => $sub->subgroups])
            @endif
        </li>
    @endforeach
</ul>
