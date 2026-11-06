<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_proyek',
        'alamat_proyek',
        'deskripsi',
        'kategori',
        'progress_percentage',
        'status',
        'main_image',
        'gallery',
        'portfolio_approved',
        'completed_at',
    ];

    protected $casts = [
        'gallery' => 'array',
        'portfolio_approved' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(UserFile::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(UserFeedback::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function portfolio()
    {
        return $this->hasOne(Portfolio::class);
    }

    protected static function booted()
    {
        static::saved(function ($project) {
            if ($project->status === 'selesai' && $project->portfolio_approved) {
                // Ambil main_image otomatis jika kosong
                $mainImage = $project->main_image;
                if (empty($mainImage)) {
                    $mainImage = $project->files()->where('type', 'progress')->latest()->value('file_path') 
                        ?? $project->files()->where('type', 'design')->latest()->value('file_path');
                }

                // Ambil gallery otomatis jika kosong
                $gallery = $project->gallery;
                if (empty($gallery)) {
                    $gallery = $project->files()->pluck('file_path')->toArray();
                }

                \App\Models\Portfolio::updateOrCreate(
                    ['project_id' => $project->id],
                    [
                        'kategori' => $project->kategori ?: 'Pembangunan Rumah',
                        'program' => $project->nama_proyek,
                        'deskripsi' => $project->deskripsi ?: '',
                        'waktu_pengerjaan' => $project->completed_at ?: now(),
                        'main_image' => $mainImage,
                        'gallery' => $gallery,
                    ]
                );
            } else {
                \App\Models\Portfolio::where('project_id', $project->id)->delete();
            }
        });

        static::deleted(function ($project) {
            \App\Models\Portfolio::where('project_id', $project->id)->delete();
        });
    }
}
