<?php

namespace App\Services\DocumentarySeries;

use App\Models\DocumentarySeries;
use Illuminate\Http\Request;
use DB;

class DocumentarySeriesService
{
    public function getIndexData(Request $request): array
    {
        $codigo = $request->input('codigo');
        $nombre = $request->input('nombre');

        $query = DocumentarySeries::query()
            ->withCount('blocks');

        if ($codigo) {
            $query->where('codigo', 'like', '%' . $codigo . '%');
        }

        if ($nombre) {
            $query->where('nombre', 'like', '%' . $nombre . '%');
        }

        $documentarySeries = $query->paginate(10);

        return compact('documentarySeries');
    }

    public function create(Request $request): DocumentarySeries
    {
        return DB::transaction(function () use ($request) {
            return DocumentarySeries::create([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
            ]);
        });
    }

    public function update(Request $request, DocumentarySeries $documentarySeries): DocumentarySeries
    {
        return DB::transaction(function () use ($request, $documentarySeries) {
            $documentarySeries->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
            ]);
            return $documentarySeries;
        });
    }

    public function delete(DocumentarySeries $documentarySeries): void
    {
        DB::transaction(function () use ($documentarySeries) {
            $documentarySeries->delete();
        });
    }
}
