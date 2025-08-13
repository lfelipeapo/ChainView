<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Process;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function processes()
    {
        return $this->belongsToMany(Process::class, 'process_tool');
    }
}
