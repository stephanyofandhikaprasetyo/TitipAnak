<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LaporanInsiden extends Model
{
    use HasFactory;

    protected $table = 'laporan_insiden';
    protected $guarded = ['id'];

    public const DISK_FOTO      = 'public';
    public const DIREKTORI_FOTO = 'laporan-insiden/bukti';

    public const KEPARAHAN_RINGAN = 'ringan';
    public const KEPARAHAN_SEDANG = 'sedang';
    public const KEPARAHAN_BERAT  = 'berat';

    public const STATUS_DITANGANI  = 'ditangani';
    public const STATUS_PEMANTAUAN = 'pemantauan';
    public const STATUS_SELESAI    = 'selesai';

    public const KEPARAHAN_LABEL = [
        self::KEPARAHAN_RINGAN => 'Ringan',
        self::KEPARAHAN_SEDANG => 'Sedang',
        self::KEPARAHAN_BERAT  => 'Berat / Darurat',
    ];

    public const KEPARAHAN_TONE = [
        self::KEPARAHAN_RINGAN => 'blue',
        self::KEPARAHAN_SEDANG => 'yellow',
        self::KEPARAHAN_BERAT  => 'coral',
    ];

    public const STATUS_LABEL = [
        self::STATUS_DITANGANI  => 'Sedang Ditangani',
        self::STATUS_PEMANTAUAN => 'Dalam Pemantauan',
        self::STATUS_SELESAI    => 'Selesai Ditangani',
    ];

    public const STATUS_TONE = [
        self::STATUS_DITANGANI  => 'coral',
        self::STATUS_PEMANTAUAN => 'yellow',
        self::STATUS_SELESAI    => 'mint',
    ];

    protected $casts = [
        'waktu_kejadian' => 'datetime',
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

    /* ------------------------------------------------------------------
     | Scope
     | ------------------------------------------------------------------ */

    public function scopeBerstatusPenanganan(Builder $query, ?string $status): Builder
    {
        if (! $status || ! array_key_exists($status, self::STATUS_LABEL)) {
            return $query;
        }

        return $query->where($this->qualifyColumn('status_penanganan'), $status);
    }

    public function scopeBerkeparahan(Builder $query, ?string $keparahan): Builder
    {
        if (! $keparahan || ! array_key_exists($keparahan, self::KEPARAHAN_LABEL)) {
            return $query;
        }

        return $query->where($this->qualifyColumn('tingkat_keparahan'), $keparahan);
    }

    /* ------------------------------------------------------------------
     | Accessor & Helper
     | ------------------------------------------------------------------ */

    protected function fotoBuktiUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->foto_bukti
                ? Storage::disk(self::DISK_FOTO)->url($this->foto_bukti)
                : null,
        );
    }

    protected function keparahanLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::KEPARAHAN_LABEL[$this->tingkat_keparahan] ?? 'Tidak Diketahui',
        );
    }

    protected function keparahanTone(): Attribute
    {
        return Attribute::make(
            get: fn () => self::KEPARAHAN_TONE[$this->tingkat_keparahan] ?? 'white',
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUS_LABEL[$this->status_penanganan] ?? 'Tidak Diketahui',
        );
    }

    protected function statusTone(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUS_TONE[$this->status_penanganan] ?? 'white',
        );
    }

    public static function daftarKeparahan(): array
    {
        return self::KEPARAHAN_LABEL;
    }

    public static function daftarStatus(): array
    {
        return self::STATUS_LABEL;
    }
}