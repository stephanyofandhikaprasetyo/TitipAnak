<?php

namespace App\Models;

// 1. Import class yang dibutuhkan
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // Tambahan untuk relasi
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Trait Spatie Permission

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Atribut yang dikesampingkan saat serialisasi (JSON/Array).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Tipe data kustom untuk atribut tertentu.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * Data anak-anak yang terhubung dengan akun orang tua/wali ini.
     */
    public function anak(): HasMany
    {
        return $this->hasMany(Anak::class);
    }

    /**
     * Riwayat/pengajuan penjemputan yang dibuat oleh orang tua/wali ini.
     */
    public function penjemputan(): HasMany
    {
        return $this->hasMany(Penjemputan::class);
    }
}