<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subgroup extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['descripcion', 'abreviacion', 'group_id', 'parent_subgroup_id'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function parentSubgroup()
    {
        return $this->belongsTo(Subgroup::class, 'parent_subgroup_id');
    }

    public function subgroups()
    {
        return $this->hasMany(Subgroup::class, 'parent_subgroup_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'subgroup_id');
    }

    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'subgroup_document_types', 'subgroup_id', 'document_type_id');
    }
}
