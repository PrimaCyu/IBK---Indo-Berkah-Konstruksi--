<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'kategori',
        'program',
        'deskripsi',
        'waktu_pengerjaan',
        'main_image',
        'gallery',
    ];

    protected $casts = [
        'gallery' => 'array',
        'waktu_pengerjaan' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
