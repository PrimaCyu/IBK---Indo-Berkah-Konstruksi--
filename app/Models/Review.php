<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'project_id',
        'message',
        'is_approved',
        'image'
    ];

    public function getReviewerNameAttribute(): string
    {
        return $this->user->name ?? ($this->guest_name ?: 'Pengunjung');
    }

    public function getReviewerEmailAttribute(): ?string
    {
        return $this->user->email ?? $this->guest_email;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
