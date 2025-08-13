<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'public.areas';

    protected $fillable = [
        'name',
    ];

    public function processes()
    {
        return $this->hasMany(Process::class);
    }
}
