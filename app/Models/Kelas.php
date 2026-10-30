<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    /**
     * Struktur tabel kelas hanya (id, nama_kelas), tanpa created_at/updated_at.
     * Hapus baris ini jika tabel Anda ternyata punya kolom timestamps.
     */
    public $timestamps = false;

    protected $fillable = [
        'nama_kelas',
    ];

    public function anak(): HasMany
    {
        return $this->hasMany(Anak::class);
    }

    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy($this->qualifyColumn('nama_kelas'));
    }
}