<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaGroupType extends Model
{
    use HasFactory;

    protected $fillable = ['area_id', 'group_type_id'];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function groupType()
    {
        return $this->belongsTo(GroupType::class, 'group_type_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'area_group_type_id');
    }
}
