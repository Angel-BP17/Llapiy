<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $casts = [
        'fecha' => 'datetime',
    ];

    protected $fillable = [
        'n_documento',
        'asunto',
        'folios',
        'root',
        'document_type_id',
        'user_id',
        'fecha',
        'periodo',
        'box_id',
        'group_id',
        'subgroup_id'
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el paquete.
     */
    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function campos()
    {
        return $this->hasMany(Campo::class, 'document_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function subgroup()
    {
        return $this->belongsTo(Subgroup::class, 'subgroup_id');
    }
}
