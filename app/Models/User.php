<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserFile;
use App\Models\Project;

#[Fillable(['name', 'email', 'phone', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['superadmin', 'admin'], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin'], true);
    }

    public function isAccounting(): bool
    {
        return $this->role === 'accounting';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function files()
    {
        return $this->hasMany(UserFile::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
  
}
