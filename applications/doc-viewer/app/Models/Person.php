<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Process;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    public function processes()
    {
        return $this->belongsToMany(Process::class, 'process_person');
    }
}
