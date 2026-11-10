<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Anak extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anak';
    protected $guarded = ['id'];

    public const STATUS_BELUM_DATANG = 'belum_datang';
    public const STATUS_HADIR        = 'hadir';
    public const STATUS_DIJEMPUT     = 'dijemput';
    public const STATUS_PULANG       = 'pulang';

    public const STATUS_LABEL = [
        self::STATUS_BELUM_DATANG => 'Belum Datang',
        self::STATUS_HADIR        => 'Hadir',
        self::STATUS_DIJEMPUT     => 'Sedang Dijemput',

        self::STATUS_PULANG       => 'Sudah Pulang',
    ];

    public const STATUS_TONE = [
        self::STATUS_BELUM_DATANG => 'ink',
        self::STATUS_HADIR        => 'blue',
        self::STATUS_DIJEMPUT     => 'yellow',
        self::STATUS_PULANG       => 'mint',
    ];

    public const STATUS_BOLEH_AJUKAN = [
        self::STATUS_BELUM_DATANG,
        self::STATUS_HADIR,
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'jam_masuk'     => 'datetime',
        'jam_pulang'    => 'datetime',
        'is_active'     => 'boolean',
    ];

    /* ------------------------------------------------------------------
     | Relasi
     | ------------------------------------------------------------------ */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo

    {
        return $this->belongsTo(Kelas::class);
    }

    public function penjemput(): HasMany
    {
        return $this->hasMany(Penjemput::class);
    }

    public function penjemputan(): HasMany
    {
        return $this->hasMany(Penjemputan::class);
    }

    public function penjemputanTerakhir(): HasOne
    {
        return $this->hasOne(Penjemputan::class)->latestOfMany();
    }

    /* ------------------------------------------------------------------
     | Scope
     | ------------------------------------------------------------------ */

    public function scopeMilikOrangTua(Builder $query, int $userId): Builder
    {
        return $query->where($this->qualifyColumn('user_id'), $userId);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('is_active'), true);
    }


    public function scopeBisaAjukanPenjemputan(Builder $query): Builder
    {
        return $query->aktif()
            ->whereIn($this->qualifyColumn('status_kehadiran'), self::STATUS_BOLEH_AJUKAN);
    }

    /* ------------------------------------------------------------------
     | Accessor
     | ------------------------------------------------------------------ */

    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->foto
                ? Storage::disk('public')->url($this->foto)
                : null,
        );
    }

    protected function namaTampil(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nama_panggilan ?: $this->nama_lengkap,
        );
    }

    protected function inisial(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::upper(Str::substr((string) $this->nama_tampil, 0, 1)),
        );

    }

    protected function umur(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->tanggal_lahir) {
                    return null;
                }

                $selisih = $this->tanggal_lahir->diff(Carbon::now());

                if ($selisih->y === 0) {
                    if ($selisih->m === 0) {
                        return $selisih->d . ' hari';
                    }

                    return $selisih->m . ' bulan';
                }

                return $selisih->m > 0
                    ? $selisih->y . ' tahun ' . $selisih->m . ' bulan'
                    : $selisih->y . ' tahun';
            },
        );
    }

    protected function statusKehadiranLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUS_LABEL[$this->status_kehadiran] ?? 'Tidak Diketahui',
        );

    }

    protected function statusKehadiranTone(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUS_TONE[$this->status_kehadiran] ?? 'white',
        );
    }

    /* ------------------------------------------------------------------
     | Helper
     | ------------------------------------------------------------------ */

    public function bolehAjukanPenjemputan(): bool
    {
        return $this->is_active
            && in_array($this->status_kehadiran, self::STATUS_BOLEH_AJUKAN, true);
    }
}