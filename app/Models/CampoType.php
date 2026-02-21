<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampoType extends Model
{
    use HasFactory;
    public const DATA_TYPES = [
        'string',
        'text',
        'char',
        'boolean',
        'int',
        'double',
        'float',
        'enum',
    ];

    protected $fillable = [
        'name',
        'data_type',
        'is_nullable',
        'length',
        'allow_negative',
        'allow_zero',
        'enum_values',
    ];

    protected $casts = [
        'is_nullable' => 'boolean',
        'allow_negative' => 'boolean',
        'allow_zero' => 'boolean',
        'enum_values' => 'array',
        'length' => 'integer',
    ];

    protected $attributes = [
        'data_type' => 'string',
        'is_nullable' => true,
        'allow_negative' => false,
        'allow_zero' => true,
    ];

    public static function dataTypes(): array
    {
        return self::DATA_TYPES;
    }

    public function campos()
    {
        return $this->hasMany(Campo::class, 'campo_type_id');
    }
    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'campo_document_types', 'campo_type_id', 'document_type_id');
    }
}
