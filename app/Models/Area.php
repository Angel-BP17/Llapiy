<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion', 'abreviacion'];

    public function areaGroupTypes()
    {
        return $this->hasMany(AreaGroupType::class, 'area_id');
    }

    public function groups()
    {
        return $this->hasManyThrough(
            Group::class,
            AreaGroupType::class,
            'area_id',
            'area_group_type_id',
            'id',
            'id'
        );
    }

    protected static function booted()
    {
        static::created(function ($area) {
            // Crear carpeta en el almacenamiento con el nombre del área
            $folderName = 'documents/' . $area->descripcion;
            Storage::makeDirectory($folderName);

            // Opcional: Mensaje en logs para depuración
            \Log::info("Carpeta creada: {$folderName}");
        });
    }
}
