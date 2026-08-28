<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentarySeries extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'documentary_series';

    protected $fillable = [
        'codigo',
        'nombre',
    ];

    public function blocks()
    {
        return $this->hasMany(Block::class, 'documentary_series_id');
    }
}
