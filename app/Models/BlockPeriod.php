<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_id',
        'rango_inicial',
        'rango_final',
        'periodo',
    ];

    protected $casts = [
        'rango_inicial' => 'integer',
        'rango_final' => 'integer',
        'periodo' => 'integer',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }
}
