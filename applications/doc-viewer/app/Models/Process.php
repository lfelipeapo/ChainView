<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Person;
use App\Models\Tool;
use App\Models\Document;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function people()
    {
        return $this->belongsToMany(Person::class, 'process_person');
    }

    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'process_tool');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
