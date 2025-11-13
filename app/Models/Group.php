<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion', 'abreviacion', 'area_group_type_id'];

    public function subgroups()
    {
        return $this->hasMany(Subgroup::class, 'group_id');
    }

    public function areaGroupType()
    {
        return $this->belongsTo(AreaGroupType::class, 'area_group_type_id');
    }
    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'group_document_types', 'group_id', 'document_type_id');
    }
}
