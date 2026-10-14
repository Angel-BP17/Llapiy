<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory, LogsActivity;

    protected static function booted(): void
    {
        static::creating(function (Block $block) {
            if (empty($block->n_bloque)) {
                $periodo = $block->periodo ?? ($block->fecha ? \Carbon\Carbon::parse($block->fecha)->year : now()->year);
                $maxBlock = self::where('periodo', $periodo)->max('n_bloque');
                $block->n_bloque = ($maxBlock ?? 0) + 1;
            }
        });
    }

    protected $casts = [
        'fecha' => 'datetime',
    ];

    protected $fillable = [
        'n_bloque',
        'asunto',
        'folios',
        'root',
        'user_id',
        'group_id',
        'subgroup_id',
        'fecha',
        'periodo',
        'box_id',
        'documentary_series_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function subgroup()
    {
        return $this->belongsTo(Subgroup::class, 'subgroup_id');
    }

    public function scopeWithoutBox($query)
    {
        return $query->whereNull('box_id');
    }

    /**
     * Scope a query to search blocks in the storage module and inbox.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchStorage($query, ?string $search = null, int|string|null $periodo = null)
    {
        $search = trim((string) $search);
        $periodo = ! empty($periodo) ? (int) $periodo : null;

        return $query
            ->when($search !== '', function ($q) use ($search, $periodo) {
                $q->where(function ($inner) use ($search, $periodo) {
                    // 1. Coincidencia por texto básico en bloques y series
                    $inner->where('n_bloque', 'like', "%{$search}%")
                        ->orWhere('asunto', 'like', "%{$search}%")
                        ->orWhereHas('documentarySeries', function ($ds) use ($search) {
                            $ds->where('codigo', 'like', "%{$search}%")
                                ->orWhere('nombre', 'like', "%{$search}%");
                        });

                    // 2. Si la búsqueda contiene o es un año de 4 dígitos (ej. "2006")
                    if (preg_match('/\b(19\d{2}|20\d{2})\b/', $search, $yearMatch)) {
                        $parsedYear = (int) $yearMatch[1];
                        $inner->orWhere('periodo', $parsedYear)
                            ->orWhereHas('periods', fn ($pQ) => $pQ->where('periodo', $parsedYear));
                    }

                    // 3. Si el usuario escribió un año y un folio combinados (ej. "2 2006", "2-2006", "2006 2")
                    if (preg_match('/\b(19\d{2}|20\d{2})\b/', $search, $yearMatch)) {
                        $parsedYear = (int) $yearMatch[1];
                        $remaining = trim(preg_replace('/\b'.preg_quote($yearMatch[1], '/').'\b/', '', $search));
                        if (preg_match('/\b(\d+)\b/', $remaining, $numMatch)) {
                            $parsedNum = (int) $numMatch[1];
                            $inner->orWhereHas('periods', function ($pQ) use ($parsedNum, $parsedYear) {
                                $pQ->where('periodo', $parsedYear)
                                    ->where('rango_inicial', '<=', $parsedNum)
                                    ->where('rango_final', '>=', $parsedNum);
                            });
                        }
                    }

                    // 4. Si el usuario escribió un rango (ej. "1-20" o "1 al 20")
                    if (preg_match('/^(\d+)\s*(?:-|al?)\s*(\d+)$/i', $search, $rangeMatches)) {
                        $start = (int) $rangeMatches[1];
                        $end = (int) $rangeMatches[2];
                        $inner->orWhereHas('periods', function ($pQ) use ($start, $end, $periodo) {
                            $pQ->where('rango_inicial', '<=', $start)
                                ->where('rango_final', '>=', $end)
                                ->when($periodo, fn ($sub) => $sub->where('periodo', $periodo));
                        });
                    }

                    // 5. Si el término de búsqueda es numérico (folio dentro del rango)
                    if (is_numeric($search)) {
                        $num = (int) $search;
                        $inner->orWhereHas('periods', function ($pQ) use ($num, $periodo) {
                            $pQ->where('rango_inicial', '<=', $num)
                                ->where('rango_final', '>=', $num)
                                ->when($periodo, fn ($sub) => $sub->where('periodo', $periodo));
                        });
                    }
                });
            })
            ->when($periodo, function ($q) use ($periodo) {
                $q->where(function ($sub) use ($periodo) {
                    $sub->where('periodo', $periodo)
                        ->orWhereYear('fecha', $periodo)
                        ->orWhereHas('periods', fn ($p) => $p->where('periodo', $periodo));
                });
            });
    }

    /**
     * Get searched blocks for global storage search bar.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getSearchedBlocks(?string $search = null, int|string|null $periodo = null, int $limit = 5): array
    {
        if (empty($search) && empty($periodo)) {
            return [];
        }

        return self::query()
            ->select(['id', 'n_bloque', 'asunto', 'folios', 'periodo', 'box_id', 'documentary_series_id'])
            ->whereNotNull('box_id')
            ->with([
                'box.andamio.section',
                'documentarySeries:id,codigo,nombre',
                'periods',
            ])
            ->searchStorage($search, $periodo)
            ->limit($limit)
            ->get()
            ->map(function ($block) {
                $ds = $block->documentarySeries ? [
                    'id' => $block->documentarySeries->id,
                    'codigo' => $block->documentarySeries->codigo,
                    'nombre' => $block->documentarySeries->nombre,
                ] : null;

                return [
                    'id' => $block->id,
                    'n_bloque' => $block->n_bloque,
                    'asunto' => $block->asunto,
                    'folios' => $block->folios,
                    'periodo' => $block->periodo,
                    'documentary_series' => $ds,
                    'documentarySeries' => $ds,
                    'periods' => $block->periods,
                    'path' => [
                        'section' => $block->box?->andamio?->section?->only(['id', 'n_section', 'descripcion']),
                        'andamio' => $block->box?->andamio?->only(['id', 'n_andamio', 'descripcion']),
                        'box' => $block->box?->only(['id', 'n_box', 'descripcion']),
                    ],
                ];
            })
            ->all();
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function documentarySeries()
    {
        return $this->belongsTo(DocumentarySeries::class, 'documentary_series_id');
    }

    public function periods()
    {
        return $this->hasMany(BlockPeriod::class, 'block_id')->orderBy('rango_inicial');
    }

    public static function getAvailableYears(): \Illuminate\Support\Collection
    {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $yearExpression = match ($driver) {
            'pgsql' => 'EXTRACT(YEAR FROM fecha)::int as year',
            'sqlite' => "strftime('%Y', fecha) as year",
            default => 'YEAR(fecha) as year',
        };

        $yearsFromDates = self::selectRaw($yearExpression)
            ->whereNotNull('fecha')
            ->distinct()
            ->pluck('year')
            ->filter();

        $yearsFromPeriodField = self::whereNotNull('periodo')
            ->distinct()
            ->pluck('periodo');

        $yearsFromPeriods = BlockPeriod::distinct()->pluck('periodo');

        return $yearsFromDates
            ->merge($yearsFromPeriodField)
            ->merge($yearsFromPeriods)
            ->map(fn ($y) => (int) $y)
            ->filter(fn ($y) => $y > 0)
            ->unique()
            ->sortDesc()
            ->values();
    }
}
