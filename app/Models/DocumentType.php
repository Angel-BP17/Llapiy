<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function campoTypes()
    {
        return $this->belongsToMany(CampoType::class, 'campo_document_types', 'document_type_id', 'campo_type_id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'document_type_id');
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_document_types', 'document_type_id', 'group_id');
    }
    public function subgroups()
    {
        return $this->belongsToMany(Subgroup::class, 'subgroup_document_types', 'document_type_id', 'subgroup_id');
    }
}
