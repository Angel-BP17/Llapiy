<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubgroupDocumentType extends Model
{
    use HasFactory;

    protected $fillable = ['document_type_id', 'subgroup_id'];

    public function subgroup()
    {
        return $this->belongsTo(Subgroup::class, 'subgroup_id');
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
}
