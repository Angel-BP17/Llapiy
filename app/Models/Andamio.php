<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Andamio extends Model
{
    use HasFactory;

    protected $fillable = ['n_andamio', 'descripcion', 'section_id'];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function boxes()
    {
        return $this->hasMany(Box::class, 'andamio_id');
    }
}