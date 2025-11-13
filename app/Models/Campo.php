<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campo extends Model
{
    use HasFactory;
    protected $fillable = ['dato', 'campo_type_id', 'document_id'];

    public function campoType()
    {
        return $this->belongsTo(CampoType::class, 'campo_type_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}
