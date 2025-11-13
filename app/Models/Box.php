<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = ['n_box', 'descripcion', 'andamio_id'];

    /**
     * Relación con el estante.
     */
    public function andamio()
    {
        return $this->belongsTo(Andamio::class, 'andamio_id');
    }

    /**
     * Relación con los paquetes.
     */
    public function blocks()
    {
        return $this->hasMany(Block::class, 'box_id');
    }
}
