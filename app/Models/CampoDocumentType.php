<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampoDocumentType extends Model
{
    use HasFactory;

    protected $table = 'campo_document_types';
    protected $fillable = ['document_type_id', 'campo_type_id'];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function campoType()
    {
        return $this->belongsTo(CampoType::class);
    }
}
