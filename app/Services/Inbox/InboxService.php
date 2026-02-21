<?php

namespace App\Services\Inbox;

use App\Models\Andamio;
use App\Models\Area;
use App\Models\Block;
use App\Models\Box;
use App\Models\Section;
use Illuminate\Http\Request;

class InboxService
{
    public function getIndexData(Request $request): array
    {
        $query = Block::withoutBox()->with(['user.group.areaGroupType.area']);

        if ($request->has('search') && !empty($request->search)) {
            $query->where('asunto', 'like', '%' . $request->search . '%');
        }

        if ($request->has('area_id') && !empty($request->area_id)) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->has('fecha') && !empty($request->fecha)) {
            $query->where('fecha', $request->fecha);
        }

        $documents = $query->latest()->paginate(10);

        $totalBlocks = Block::count();
        $attendedBlocksCount = Block::query()
            ->whereNotNull('root')
            ->where('root', '!=', '')
            ->whereNotNull('box_id')
            ->whereHas('box.andamio')
            ->count();
        $unattendedBlocksCount = max($totalBlocks - $attendedBlocksCount, 0);

        $areas = Area::all();
        $fechas = Block::select('fecha')->distinct()->pluck('fecha');

        $periodos = $fechas->map(function ($fecha) {
            return \Carbon\Carbon::parse($fecha)->year;
        })->unique()->values();

        $sections = Section::all();
        $andamios = Andamio::all();
        $boxes = Box::all();

        return compact(
            'documents',
            'areas',
            'fechas',
            'periodos',
            'sections',
            'andamios',
            'boxes',
            'attendedBlocksCount',
            'unattendedBlocksCount'
        );
    }

    public function updateBlockStorage(Request $request, int $id): void
    {
        $document = Block::findOrFail($id);
        $document->box_id = $request->n_box;
        $document->save();
    }
}
