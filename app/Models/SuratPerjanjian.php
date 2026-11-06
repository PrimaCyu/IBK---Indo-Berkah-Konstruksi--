<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPerjanjian extends Model
{
    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'keterangan',
        'file_surat',
        'status',
        'catatan_admin',
    ];
}
