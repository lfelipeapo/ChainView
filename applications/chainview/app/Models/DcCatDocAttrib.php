<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcCatDocAttrib extends Model
{
    use HasFactory;

    protected $table = 'public.dccatdocattrib';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cdcategory',
        'cdattribute',
        'fgrequired',
        'nrorder',
        'nmdefaultvalue',
        'vldefaultvalue',
    ];
}
