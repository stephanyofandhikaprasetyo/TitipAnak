<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Penjemput extends Model
{
    use HasFactory;

    /**
     * Nama tabel diset manual karena Laravel akan menebak "penjemputs".
     */
    protected $table = 'penjemput';

    /**
     * Foto KTP penjemput adalah data sensitif, jadi disimpan di disk PRIVATE
     * (storage/app/private atau storage/app, sesuai versi Laravel) dan
     * ditampilkan lewat route terproteksi, bukan URL publik.
     */
    public const DISK_FOTO = 'local';
    public const DIREKTORI_FOTO = 'penjemput/identitas';

    protected $fillable = [
        'anak_id',
        'nama',
        'hubungan',
        'nik',
        'no_hp',
        'foto',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * NIK tidak ikut terserialisasi ke JSON/array secara tidak sengaja.
     */
    protected $hidden = [
        'nik',
    ];

    /* ------------------------------------------------------------------
     | Relasi
     | ------------------------------------------------------------------ */

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }

    public function penjemputan(): HasMany
    {
        return $this->hasMany(Penjemputan::class);
    }

    /* ------------------------------------------------------------------
     | Scope
     | ------------------------------------------------------------------ */

    /**
     * Penjemput rutin yang sah dan masih aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('is_active'), true);
    }

    /**
     * Penjemput yang tidak aktif. Penjemput darurat dari orang tua disimpan
     * dengan is_active = false agar tidak muncul sebagai penjemput rutin.
     */
    public function scopeNonaktif(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('is_active'), false);
    }

    /**
     * Batasi hanya untuk penjemput dari anak milik orang tua tertentu.
     */
    public function scopeMilikOrangTua(Builder $query, int $userId): Builder
    {
        return $query->whereHas('anak', function (Builder $q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /* ------------------------------------------------------------------
     | Accessor
     | ------------------------------------------------------------------ */

    /**
     * Satu huruf awal untuk avatar bulat.
     */
    protected function inisial(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::upper(Str::substr((string) $this->nama, 0, 1)),
        );
    }

    /**
     * NIK yang disamarkan untuk ditampilkan, contoh: "************3456".
     * Mengembalikan null jika NIK kosong (penjemput darurat tidak wajib NIK).
     */
    protected function nikSamar(): Attribute
    {
        return Attribute::make(
            get: function () {
                $nik = (string) $this->getRawOriginal('nik');

                if ($nik === '') {
                    return null;
                }

                return str_repeat('*', max(strlen($nik) - 4, 0)) . substr($nik, -4);
            },
        );
    }

    /* ------------------------------------------------------------------
     | Helper
     | ------------------------------------------------------------------ */

    public function punyaFoto(): bool
    {
        return filled($this->foto);
    }
}