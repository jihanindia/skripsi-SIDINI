<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const PUSKESMAS_PULO_ARMYN = 'Puskesmas Pulo Armyn';
    public const PUSKESMAS_PANCASAN = 'Puskesmas Pancasan';

    public const ROLE_DINAS = 'dinas';
    public const ROLE_PUSKESMAS = 'puskesmas';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'puskesmas',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isDinas(): bool
    {
        return $this->role === self::ROLE_DINAS;
    }

    public function isPuskesmas(): bool
    {
        return $this->role === self::ROLE_PUSKESMAS;
    }

    public function getRoleLabelAttribute(): string
    {
        if ($this->isDinas()) {
            return 'Dinas Kesehatan Kota Bogor';
        }

        if ($this->isPuskesmas() && $this->puskesmas) {
            return $this->puskesmas;
        }

        return ucfirst($this->role ?? 'User');
    }
}
