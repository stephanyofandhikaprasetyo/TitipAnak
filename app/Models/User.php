<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['nama', 'email', 'password', 'aktif'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Nama tabel di database sesuai standar main branch.
     */
    protected $table = 'pengguna';

    /**
     * Guard name untuk Spatie Laravel Permission.
     */
    protected string $guard_name = 'web';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    /**
     * Relasi ke profil orang tua (jika pengguna memiliki role orang tua).
     */
    public function profilOrangTua(): HasOne
    {
        return $this->hasOne(ProfilOrangTua::class, 'pengguna_id');
    }

    /**
     * Relasi ke profil pengasuh (jika pengguna memiliki role pengasuh).
     */
    public function profilPengasuh(): HasOne
    {
        return $this->hasOne(ProfilPengasuh::class, 'pengguna_id');
    }

    /**
     * Relasi ke daftar menu makan yang dibuat oleh pengguna.
     */
    public function menuMakan(): HasMany
    {
        return $this->hasMany(MenuMakan::class, 'dibuat_oleh_id');
    }
}