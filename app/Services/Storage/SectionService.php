<?php

namespace App\Services\Storage;

use App\Models\Block;
use App\Models\Box;
use App\Models\Section;

class SectionService
{
    /**
     * Obtiene todas las secciones con conteo de andamios, optimizado para listado.
     */
    public function getAll(?string $search = null)
    {
        return Section::query()
            ->select(['id', 'n_section', 'descripcion', 'created_at'])
            ->withCount('andamios')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('n_section', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%")
                        ->orWhereHas('andamios.boxes.blocks', function ($q) use ($search) {
                            $q->where('n_bloque', 'like', "%{$search}%")
                                ->orWhere('asunto', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('n_section')
            ->paginate(10)
            ->withQueryString();
    }

    public function getStorageStats(): array
    {
        $totalBlocks = Block::count();
        $archivedBlocks = Block::whereNotNull('box_id')->count();
        $indexedBlocks = Block::whereNotNull('root')->where('root', '!=', '')->count();
        $bothArchivedAndIndexed = Block::whereNotNull('box_id')
            ->whereNotNull('root')
            ->where('root', '!=', '')
            ->count();

        $totalBoxes = Box::count();
        $filledBoxes = Box::whereHas('blocks')->count();
        $emptyBoxes = max($totalBoxes - $filledBoxes, 0);

        return [
            'totalBlocks' => $totalBlocks,
            'archivedBlocks' => $archivedBlocks,
            'indexedBlocks' => $indexedBlocks,
            'bothArchivedAndIndexed' => $bothArchivedAndIndexed,
            'totalBoxes' => $totalBoxes,
            'filledBoxes' => $filledBoxes,
            'emptyBoxes' => $emptyBoxes,
        ];
    }

    public function getReportData(): array
    {
        $stats = $this->getStorageStats();

        $areaBreakdown = Block::query()
            ->with([
                'group.areaGroupType.area:id,descripcion',
                'group:id,descripcion',
                'subgroup:id,descripcion',
            ])
            ->get()
            ->groupBy(function ($block) {
                $area = $block->group?->areaGroupType?->area?->descripcion ?? 'Sin área';
                $group = $block->group?->descripcion ?? 'Sin grupo';
                $subgroup = $block->subgroup?->descripcion ?? 'Sin subgrupo';

                return "{$area} > {$group} > {$subgroup}";
            })
            ->map(function ($blocks, $key) {
                $parts = explode(' > ', $key);
                $indexedCount = $blocks->filter(fn ($b) => ! empty($b->root))->count();
                $archivedCount = $blocks->filter(fn ($b) => ! empty($b->box_id))->count();
                $totalFolios = $blocks->sum('folios');

                return [
                    'area' => $parts[0] ?? 'Sin área',
                    'group' => $parts[1] ?? 'Sin grupo',
                    'subgroup' => $parts[2] ?? 'Sin subgrupo',
                    'total_blocks' => $blocks->count(),
                    'archived_blocks' => $archivedCount,
                    'indexed_blocks' => $indexedCount,
                    'total_folios' => $totalFolios,
                ];
            })
            ->values();

        $boxesDetail = Box::query()
            ->with(['andamio.section'])
            ->withCount('blocks')
            ->withSum('blocks', 'folios')
            ->orderBy('n_box')
            ->get();

        return [
            'stats' => $stats,
            'areaBreakdown' => $areaBreakdown,
            'boxesDetail' => $boxesDetail,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
        ];
    }

    public function create(array $data): Section
    {
        return Section::create($data);
    }

    public function update(Section $section, array $data): Section
    {
        $section->update($data);

        return $section->fresh();
    }

    public function delete(Section $section): void
    {
        $section->delete();
    }
}
