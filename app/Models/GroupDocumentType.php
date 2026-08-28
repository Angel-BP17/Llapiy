<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupDocumentType extends Model
{
    use HasFactory;

    protected $fillable = ['document_type_id', 'group_id'];

    public function subgroup()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
}
