<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
    use HasFactory;

    protected $table = 'penawarans';

    protected $fillable = [
        'nama',
        'telepon',
        'email',
        'foto',
        'deskripsi',
        'budget',
        'alamat',
        'status',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}