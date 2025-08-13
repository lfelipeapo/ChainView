<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcDocumentAttrib extends Model
{
    use HasFactory;

    protected $table = 'public.dcdocumentattrib';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'numero_doc',
        'cdattribute',
        'nmuserupd',
        'cdrevision',
        'cdcategory'
    ];
}
