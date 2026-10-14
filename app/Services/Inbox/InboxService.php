<?php

namespace App\Services\Inbox;

use App\Models\Andamio;
use App\Models\Area;
use App\Models\Block;
use App\Models\Box;
use App\Models\Section;
use App\Support\PhpIniHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InboxService
{
    public function getIndexData(Request $request): array
    {
        $search = $request->input('search');
        $periodo = $request->filled('periodo') ? (int) $request->input('periodo') : ($request->filled('fecha') ? (int) $request->input('fecha') : null);

        $query = Block::withoutBox()->with([
            'user:id,name,last_name,group_id',
            'user.group:id,area_group_type_id,descripcion',
            'user.group.areaGroupType:id,area_id',
            'user.group.areaGroupType.area:id,descripcion',
            'documentarySeries:id,codigo,nombre',
            'periods',
        ]);

        $query->searchStorage($search, $periodo);

        if ($request->has('area_id') && ! empty($request->area_id)) {
            $query->whereHas('group.areaGroupType', function ($inner) use ($request) {
                $inner->where('area_id', $request->area_id);
            });
        }

        $paginatedDocuments = $query->latest()->paginate(10)->withQueryString();

        $totalBlocks = Block::count();
        $attendedBlocksCount = Block::query()
            ->whereNotNull('root')
            ->where('root', '!=', '')
            ->whereNotNull('box_id')
            ->whereHas('box.andamio')
            ->count();
        $unattendedBlocksCount = max($totalBlocks - $attendedBlocksCount, 0);

        $areas = Area::select('id', 'descripcion')->get();

        $fechas = Block::whereNotNull('fecha')->distinct()->pluck('fecha');
        $periodos = Block::getAvailableYears()->map(fn ($y) => (string) $y)->values();

        $sections = Section::select('id', 'n_section', 'descripcion')->get();
        $andamios = Andamio::select('id', 'n_andamio', 'section_id')->get();
        $boxes = Box::select('id', 'n_box', 'andamio_id')->get();

        return [
            'documents' => $paginatedDocuments->items(),
            'pagination' => [
                'total' => $paginatedDocuments->total(),
                'current_page' => $paginatedDocuments->currentPage(),
                'last_page' => $paginatedDocuments->lastPage(),
                'from' => $paginatedDocuments->firstItem(),
                'to' => $paginatedDocuments->lastItem(),
            ],
            'filters' => [
                'search' => $search ?? '',
                'area_id' => $request->area_id ? (string) $request->area_id : '',
                'periodo' => $periodo ? (string) $periodo : '',
            ],
            'areas' => $areas,
            'fechas' => $fechas,
            'periodos' => $periodos,
            'sections' => $sections,
            'andamios' => $andamios,
            'boxes' => $boxes,
            'attendedBlocksCount' => $attendedBlocksCount,
            'unattendedBlocksCount' => $unattendedBlocksCount,
            'maxUploadSize' => PhpIniHelper::getMaxUploadFileSize(),
            'maxUploadSizeFormatted' => PhpIniHelper::getMaxUploadFileSizeFormatted(),
        ];
    }

    public function updateBlockStorage(Request $request, int $id): void
    {
        $document = Block::findOrFail($id);
        $document->box_id = $request->n_box;
        $document->save();
    }

    public function deleteBlockFile(int $id): void
    {
        $block = Block::findOrFail($id);

        if ($block->root) {
            if (Storage::disk('local')->exists($block->root)) {
                Storage::disk('local')->delete($block->root);
            }
            if (Storage::disk('public')->exists($block->root)) {
                Storage::disk('public')->delete($block->root);
            }
            $block->root = null;
            $block->save();
        }
    }
}
