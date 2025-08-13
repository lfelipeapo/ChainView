<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcDocRevision extends Model
{
    use HasFactory;

    protected $table = 'public.dcdocrevision';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cdrevision',
        'fgcurrent',
        'fgtrainrequired',
        'dssummary',
        'nmtitle',
        'iddocument',
        'cdcategory',
    ];
}
