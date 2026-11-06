<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama',
        'nama_pendek',
        'foto',
        'profil_singkat',
        'email',
        'telepon',
        'alamat',
        'posisi',
        'is_visible',
        'status',
        'start_date',
        'rejection_reason',
    ];
    
    protected $casts = [
        'is_visible' => 'boolean',
        'start_date' => 'date',
    ];

    /**
     * Nama yang ditampilkan di publik (nama pendek, atau fallback ke nama lengkap).
     */
    public function getNamaTampilAttribute(): string
    {
        return $this->nama_pendek ?: $this->nama;
    }
}
