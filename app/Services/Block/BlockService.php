<?php
namespace App\Services\Block;

use App\Models\Area;
use App\Models\Block;
use App\Models\Group;
use App\Models\Subgroup;
use Auth;
use Carbon\Carbon;
use DB;
use Storage;
use Str;

class BlockService
{
    public function getAll($data)
    {
        $query = Block::query()->with(['group.areaGroupType.area', 'subgroup', 'user'])
            ->when(
                !Auth::user()->isAdminOrManager(),
                fn($q) => $q->when('group_id', fn($q) => $q->when('subgroup_id', fn($q) => $q->where('group_id', Auth::user()->group_id)))->when('subgroup_id', fn($q) => $q->where('subgroup_id', Auth::user()->subgroup_id))
            )
            ->when(
                $data->asunto,
                fn($q, $asunto) => $q->where('asunto', 'LIKE', "%{$asunto}%")
            )
            ->when(
                $data->area_id,
                fn($q, $areaId) => $q->whereHas('group.areaGroupType.area', function ($q) use ($areaId) {
                    $q->where('id', $areaId);
                })
            )
            ->when(
                $data->group_id,
                fn($q, $groupId) => $q->whereHas('documentType.groups', function ($q) use ($groupId) {
                    $q->where('groups.id', $groupId);
                })
            )
            ->when(
                $data->subgroup_id,
                fn($q, $subgroupId) => $q->whereHas('documentType.subgroups', function ($q) use ($subgroupId) {
                    $q->where('subgroups.id', $subgroupId);
                })
            )
            ->when(
                $data->year,
                fn($q, $year) => $q->whereYear('fecha', $year)
            )
            ->when(
                $data->month,
                fn($q, $month) => $q->whereMonth('fecha', $month)
            );

        $years = Block::selectRaw('YEAR(fecha) as year')->distinct()->pluck('year');

        return [
            'blocks' => $query,
            'areas' => Area::with('groups.subgroups')->get(),
            'groups' => Group::all(),
            'subgroups' => Subgroup::all(),
            'years' => $years,
        ];
    }

    public function create(array $data, $file)
    {
        return DB::transaction(function () use ($data, $file) {

            $filePath = $this->storeBlockFile($file, $data['asunto']);

            $block = Block::create([
                'n_bloque' => $data['n_bloque'],
                'asunto' => $data['asunto'],
                'folios' => $data['folios'],
                'root' => $filePath,
                'rango_inicial' => $data['rango_inicial'],
                'rango_final' => $data['rango_final'],
                'user_id' => Auth::id(),
                'group_id' => Auth::user()->group_id,
                'subgroup_id' => Auth::user()->subgroup_id,
                'fecha' => $data['fecha'],
                'periodo' => Carbon::parse($data['fecha'])->year,
            ]);

            return $block;
        });
    }

    public function update($data, $file, $hasFile, $block)
    {
        return DB::transaction(function () use ($data, $file, $hasFile, $block) {
            if ($hasFile) {
                Storage::delete("public/{$data['root']}");
                $data['root'] = $this->storeBlockFile($file, $data['asunto']);
            }

            $block->update([
                'n_bloque' => $data['n_bloque'] ?? $block->n_bloque,
                'asunto' => $data['asunto'] ?? $block->asunto,
                'folios' => $data['folios'] ?? $block->folios,
                'fecha' => $data['fecha'] ?? $block->fecha,
                'root' => $data['root'] ?? $block->root,
                'periodo' => Carbon::parse($data['fecha'])->year ?? $block->periodo,
                'group_id' => Auth::user()->group_id,
                'subgroup' => Auth::user()->subgroup_id,
            ]);

            return $block;
        });
    }

    public function delete($block)
    {
        return DB::transaction(function () use ($block) {
            $filePath = $block->root;
            $block->delete();
            Storage::delete("public/{$filePath}");
        });
    }

    public function report($data)
    {
        return Block::query()->with(['group.areaGroupType.area', 'subgroup', 'user'])
            ->when(
                !Auth::user()->isAdminOrManager(),
                fn($q) => $q->when('group_id', fn($q) => $q->when('sugroup_id', fn($q) => $q->where('group_id', Auth::user()->group_id)))->when('subgroup_id', fn($q) => $q->where('subgroup_id', Auth::user()->subgroup_id))
            )
            ->when(
                $data->asunto,
                fn($q, $asunto) => $q->where('asunto', 'like', "%{$asunto}%")
            )
            ->when(
                $data->area_id,
                fn($q, $areaId) => $q->whereHas('group.areaGroupType.area', function ($q) use ($areaId) {
                    $q->where('id', $areaId);
                })
            )
            ->when(
                $data->group_id,
                fn($q, $groupId) => $q->whereHas('documentType.groups', function ($q) use ($groupId) {
                    $q->where('groups.id', $groupId);
                })
            )
            ->when(
                $data->subgroup_id,
                fn($q, $subgroupId) => $q->whereHas('documentType.subgroups', function ($q) use ($subgroupId) {
                    $q->where('subgroups.id', $subgroupId);
                })
            )
            ->when(
                $data->year,
                fn($q, $year) => $q->whereYear('fecha', $year)
            )
            ->when(
                $data->month,
                fn($q, $month) => $q->whereMonth('fecha', $month)
            );
    }

    private function storeBlockFile($file, string $asunto): string
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($asunto) . '_' . time() . '.' . $extension;
        $area = Auth::user()->group->areaGroupType->area->descripcion ?? "Sin_area";
        $folderPath = "blocks/{$area}";

        return $file->storeAs($folderPath, $fileName, 'public');
    }
}
