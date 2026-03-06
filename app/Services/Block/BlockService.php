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
    public function getShowData(Block $block): array
    {
        $block->load([
            'group.areaGroupType.area',
            'subgroup',
            'user:id,name,last_name',
            'box.andamio.section',
        ]);

        return compact('block');
    }

    public function getAll($data)
    {
        $user = Auth::user();
        $query = Block::query()
            ->select([
                'id', 'n_bloque', 'asunto', 'folios', 'root', 'rango_inicial', 'rango_final', 
                'user_id', 'group_id', 'subgroup_id', 'box_id', 'fecha', 'periodo', 'created_at'
            ])
            ->when(!$user->hasRole('ADMINISTRADOR'), function ($q) use ($user) {
                if ($user->can('blocks.view.all')) {
                    return $q;
                }

                if ($user->can('blocks.view.group')) {
                    if ($user->subgroup_id) {
                        return $q->where('subgroup_id', $user->subgroup_id);
                    }
                    return $q->where('group_id', $user->group_id);
                }

                if ($user->can('blocks.view.own')) {
                    return $q->where('user_id', $user->id);
                }

                return $q->whereRaw('1 = 0');
            })
            ->with([
                'group:id,area_group_type_id,descripcion',
                'group.areaGroupType:id,area_id',
                'group.areaGroupType.area:id,descripcion',
                'subgroup:id,descripcion',
                'user:id,name,last_name',
                'box:id,n_box,andamio_id',
                'box.andamio:id,n_andamio,section_id',
                'box.andamio.section:id,n_section'
            ])
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
                $data->role_id,
                fn($q, $roleId) => $q->whereHas('user.roles', function ($q) use ($roleId) {
                    $q->where('roles.id', $roleId);
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

        $driver = DB::connection()->getDriverName();
        $yearExpression = $driver === 'pgsql'
            ? 'EXTRACT(YEAR FROM fecha)::int as year'
            : 'YEAR(fecha) as year';

        $years = \Illuminate\Support\Facades\Cache::remember('blocks_available_years', now()->addHours(24), function () use ($yearExpression) {
            return Block::selectRaw($yearExpression)
                ->whereNotNull('fecha')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
        });

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

            $filePath = $file ? $this->storeBlockFile($file, $data['asunto']) : null;

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

            \Illuminate\Support\Facades\Cache::forget('blocks_available_years');

            return $block;
        });
    }

    public function update($data, $file, $hasFile, $block)
    {
        return DB::transaction(function () use ($data, $file, $hasFile, $block) {
            if ($hasFile) {
                if ($block->root) {
                    \App\Jobs\DeleteFileJob::dispatch($block->root);
                }
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
                'subgroup_id' => Auth::user()->subgroup_id,
            ]);

            return $block;
        });
    }

    public function delete($block)
    {
        return DB::transaction(function () use ($block) {
            $filePath = $block->root;
            $block->delete();
            if ($filePath) {
                \App\Jobs\DeleteFileJob::dispatch($filePath);
            }
        });
    }

    public function uploadFile(Block $model, $file): Block
    {
        return DB::transaction(function () use ($model, $file) {
            $block = Block::lockForUpdate()->findOrFail($model->id);

            if ($block->root) {
                \App\Jobs\DeleteFileJob::dispatch($block->root);
            }

            $block->update([
                'root' => $this->storeBlockFile($file, $block->asunto),
            ]);

            return $block;
        });
    }

    public function report($data)
    {
        return Block::query()->with(['group.areaGroupType.area', 'subgroup', 'user'])
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
                $data->role_id,
                fn($q, $roleId) => $q->whereHas('user.roles', function ($q) use ($roleId) {
                    $q->where('roles.id', $roleId);
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
        // Usar extension() es más seguro que getClientOriginalExtension()
        $extension = $file->extension() ?: $file->getClientOriginalExtension();
        
        // Añadir milisegundos y un string aleatorio para evitar colisiones en alta concurrencia
        $safeAsunto = Str::limit(Str::slug($asunto), 100, '');
        $fileName = $safeAsunto . '_' . now()->getTimestampMs() . '_' . Str::random(5) . '.' . $extension;
        
        $user = Auth::user();
        $areaName = $user?->group?->areaGroupType?->area?->descripcion ?? "Sin_area";
        $folderPath = "blocks/" . Str::slug($areaName);

        return $file->storeAs($folderPath, $fileName, 'public');
    }
}
