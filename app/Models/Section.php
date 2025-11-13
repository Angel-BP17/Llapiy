<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'n_section',
        'descripcion',
    ];

    public function andamios()
    {
        return $this->hasMany(Andamio::class, 'section_id');
    }
}
