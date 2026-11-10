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

    protected $table = 'penjemput';
    protected $guarded = ['id'];

    public const DISK_FOTO = 'local';
    public const DIREKTORI_FOTO = 'penjemput/identitas';

    protected $casts = [
        'is_active' => 'boolean',
    ];

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

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('is_active'), true);
    }

    public function scopeNonaktif(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('is_active'), false);
    }

    public function scopeMilikOrangTua(Builder $query, int $userId): Builder
    {
        return $query->whereHas('anak', function (Builder $q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }


    /* ------------------------------------------------------------------
     | Accessor
     | ------------------------------------------------------------------ */

    protected function inisial(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::upper(Str::substr((string) $this->nama, 0, 1)),
        );
    }

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