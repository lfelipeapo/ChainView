<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdAttribute extends Model
{
    use HasFactory;

    protected $table = 'public.adattribute';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cdattribute',
        'nmattribute',
        'nmlabel',
    ];
}
