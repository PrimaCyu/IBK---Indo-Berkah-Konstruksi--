<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPerjanjian extends Model
{
    protected $fillable = [
        'project_id',
        'nama',
        'email',
        'telepon',
        'keterangan',
        'file_surat',
        'status',
        'catatan_admin',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
