<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFile extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'description',
        'file_path',
        'type',  // 'design' = user upload | 'progress' = admin upload
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    // Scope: hanya desain user
    public function scopeDesigns($query)
    {
        return $query->where('type', 'design');
    }

    // Scope: hanya foto progres dari admin
    public function scopeProgress($query)
    {
        return $query->where('type', 'progress');
    }
}