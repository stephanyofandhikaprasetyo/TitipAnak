<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanHarian extends Model
{
    use HasFactory;

    protected $table = 'laporan_harian';
    protected $guarded = ['id'];

    public const KONDISI_SEHAT      = 'sehat';
    public const KONDISI_KURANG_FIT = 'kurang_fit';
    public const KONDISI_SAKIT      = 'sakit';
    public const KONDISI_REWEL      = 'rewel';

    public const KONDISI_LABEL = [
        self::KONDISI_SEHAT      => 'Sehat & Ceria',
        self::KONDISI_KURANG_FIT => 'Kurang Fit',
        self::KONDISI_SAKIT      => 'Sakit',
        self::KONDISI_REWEL      => 'Rewel',
    ];

    public const KONDISI_TONE = [
        self::KONDISI_SEHAT      => 'mint',
        self::KONDISI_KURANG_FIT => 'yellow',
        self::KONDISI_SAKIT      => 'coral',
        self::KONDISI_REWEL      => 'lavender',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /* ------------------------------------------------------------------
     | Relasi
     | ------------------------------------------------------------------ */

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }

    public function pengasuh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengasuh_id');
    }

    public function foto(): HasMany
    {
        return $this->hasMany(LaporanHarianFoto::class, 'laporan_harian_id');
    }

    /* ------------------------------------------------------------------
     | Scope
     | ------------------------------------------------------------------ */

    public function scopeOlehPengasuh(Builder $query, int $pengasuhId): Builder
    {
        return $query->where($this->qualifyColumn('pengasuh_id'), $pengasuhId);
    }

    public function scopePadaTanggal(Builder $query, ?string $tanggal): Builder
    {
        if (! $tanggal) {
            return $query;
        }

        return $query->whereDate($this->qualifyColumn('tanggal'), $tanggal);
    }

    public function scopeBerstatusKondisi(Builder $query, ?string $kondisi): Builder
    {
        if (! $kondisi || ! array_key_exists($kondisi, self::KONDISI_LABEL)) {
            return $query;
        }

        return $query->where($this->qualifyColumn('kondisi'), $kondisi);
    }

    /* ------------------------------------------------------------------
     | Accessor & Helper
     | ------------------------------------------------------------------ */

    protected function kondisiLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::KONDISI_LABEL[$this->kondisi] ?? 'Tidak Diketahui',
        );
    }

    protected function kondisiTone(): Attribute
    {
        return Attribute::make(
            get: fn () => self::KONDISI_TONE[$this->kondisi] ?? 'white',
        );
    }

    public static function daftarKondisi(): array
    {
        return self::KONDISI_LABEL;
    }
}