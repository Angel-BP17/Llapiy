<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampoType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function campos()
    {
        return $this->hasMany(Campo::class, 'campo_type_id');
    }
    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'campo_document_types', 'campo_type_id', 'document_type_id');
    }
}
