<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;

    protected $table = 'public.processes';

    protected $fillable = [
        'area_id',
        'parent_id',
        'name',
        'description',
        'type',
        'criticality',
        'status',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function parent()
    {
        return $this->belongsTo(Process::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Process::class, 'parent_id')->with('children');
    }
}
