<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Process;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'title',
        'content',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
