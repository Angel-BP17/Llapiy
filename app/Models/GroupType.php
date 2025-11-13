<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupType extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion', 'abreviacion'];

    public function areaGroupTypes()
    {
        return $this->hasMany(AreaGroupType::class, 'group_type_id');
    }

    public function canBeDeleted()
    {
        // Verifica si tiene grupos asociados
        return $this->areaGroupTypes->every(function ($agt) {
            return $agt->groups->isEmpty();
        });
    }
}
