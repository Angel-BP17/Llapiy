<div class="modal fade" id="modalBefore{{ $log->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos Antes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm">
                    <tbody>
                        @foreach ($log->before as $key => $value)
                            <tr>
                                <th>{{ ucfirst($key) }}</th>
                                <td>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
