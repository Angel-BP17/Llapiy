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
        'rango_inicial',
        'rango_final',
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
