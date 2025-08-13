<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'public.dccategory';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cdcategory',
        'cdcategoryowner',
        'nmcategory',
        'idcategory',
        'fglogo',
        'fgenablephysfile',
    ];
}
