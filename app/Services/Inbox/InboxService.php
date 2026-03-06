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
        $query = Block::withoutBox()->with([
            'user:id,name,last_name,group_id', 
            'user.group:id,area_group_type_id,descripcion', 
            'user.group.areaGroupType:id,area_id', 
            'user.group.areaGroupType.area:id,descripcion'
        ]);

        if ($request->has('search') && !empty($request->search)) {
            $query->where('asunto', 'like', '%' . $request->search . '%');
        }

        if ($request->has('area_id') && !empty($request->area_id)) {
            $query->whereHas('group.areaGroupType', function ($inner) use ($request) {
                $inner->where('area_id', $request->area_id);
            });
        }

        if ($request->has('fecha') && !empty($request->fecha)) {
            $query->whereYear('fecha', (int) $request->fecha);
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

        $areas = \Illuminate\Support\Facades\Cache::remember('areas_list_simple', now()->addDay(), function() {
            return Area::select('id', 'descripcion')->get();
        });
        
        $fechas = \Illuminate\Support\Facades\Cache::remember('inbox_fechas', now()->addHours(24), function() {
            return Block::select('fecha')->distinct()->pluck('fecha');
        });

        $periodos = $fechas->map(function ($fecha) {
            return \Carbon\Carbon::parse($fecha)->year;
        })->unique()->values();

        // Estas listas pueden ser pesadas, se aconseja al front pedirlas asíncronamente
        // pero por ahora las cacheamos para aliviar la base de datos
        $sections = \Illuminate\Support\Facades\Cache::remember('sections_list_all', now()->addDay(), fn() => Section::select('id', 'n_section', 'descripcion')->get());
        $andamios = \Illuminate\Support\Facades\Cache::remember('andamios_list_all', now()->addDay(), fn() => Andamio::select('id', 'n_andamio', 'section_id')->get());
        $boxes = \Illuminate\Support\Facades\Cache::remember('boxes_list_all', now()->addDay(), fn() => Box::select('id', 'n_box', 'andamio_id')->get());

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
