<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjemputan extends Model
{
    use HasFactory;

    protected $table = 'penjemputan';
    protected $guarded = ['id'];

    public const DISK_FOTO      = 'local';
    public const DIREKTORI_FOTO = 'penjemputan/identitas';

    public const STATUS_PENDING    = 'pending';
    public const STATUS_DISETUJUI  = 'disetujui';
    public const STATUS_DITOLAK    = 'ditolak';
    public const STATUS_SELESAI    = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const METODE_KTP     = 'ktp';
    public const METODE_PIN     = 'pin';
    public const METODE_DARURAT = 'darurat';

    public const STATUS_LABEL = [
        self::STATUS_PENDING    => 'Menunggu Verifikasi',
        self::STATUS_DISETUJUI  => 'Disetujui',
        self::STATUS_DITOLAK    => 'Ditolak',
        self::STATUS_SELESAI    => 'Selesai',
        self::STATUS_DIBATALKAN => 'Dibatalkan',
    ];

    public const STATUS_TONE = [
        self::STATUS_PENDING    => 'yellow',
        self::STATUS_DISETUJUI  => 'mint',
        self::STATUS_DITOLAK    => 'coral',
        self::STATUS_SELESAI    => 'blue',
        self::STATUS_DIBATALKAN => 'ink',
    ];

    public const STATUS_KETERANGAN = [
        self::STATUS_PENDING    => 'Pengajuan sedang diperiksa oleh admin atau pengasuh.',
        self::STATUS_DISETUJUI  => 'Penjemput diizinkan menjemput anak Anda dengan membawa KTP.',
        self::STATUS_DITOLAK    => 'Pengajuan tidak disetujui. Silakan hubungi pihak daycare.',
        self::STATUS_SELESAI    => 'Anak sudah selesai dijemput oleh penjemput ini.',
        self::STATUS_DIBATALKAN => 'Pengajuan penjemputan telah dibatalkan oleh orang tua.',
    ];

    public const METODE_LABEL = [
        self::METODE_KTP     => 'Verifikasi KTP Fisik',
        self::METODE_PIN     => 'Kode PIN / Konfirmasi Wali',
        self::METODE_DARURAT => 'Penjemputan Darurat',
    ];

    protected $casts = [
        'waktu_jemput' => 'datetime',
    ];

    /* ------------------------------------------------------------------
     | Relasi
     | ------------------------------------------------------------------ */

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }

    public function penjemput(): BelongsTo
    {
        return $this->belongsTo(Penjemput::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ------------------------------------------------------------------
     | Scope
     | ------------------------------------------------------------------ */

    public function scopeMilikOrangTua(Builder $query, int $userId): Builder
    {
        return $query->where($this->qualifyColumn('user_id'), $userId);
    }

    public function scopeDarurat(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('metode_verifikasi'), self::METODE_DARURAT);
    }

    public function scopeBerstatus(Builder $query, ?string $status): Builder
    {
        if (! $status || ! array_key_exists($status, self::STATUS_LABEL)) {
            return $query;
        }

        return $query->where($this->qualifyColumn('status'), $status);
    }

    public function scopeMenunggu(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_PENDING);
    }

    /* ------------------------------------------------------------------
     | Accessor
     | ------------------------------------------------------------------ */

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUS_LABEL[$this->status] ?? 'Tidak Diketahui',
        );
    }

    protected function statusTone(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUS_TONE[$this->status] ?? 'white',
        );
    }

    protected function statusKeterangan(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUS_KETERANGAN[$this->status] ?? '',
        );
    }

    protected function metodeVerifikasiLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::METODE_LABEL[$this->metode_verifikasi] ?? strtoupper((string) $this->metode_verifikasi),
        );
    }

    /* ------------------------------------------------------------------
     | Helper
     | ------------------------------------------------------------------ */

    public function isDarurat(): bool
    {
        return $this->metode_verifikasi === self::METODE_DARURAT;
    }

    public function isMenunggu(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isDisetujui(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public function isDibatalkan(): bool
    {
        return $this->status === self::STATUS_DIBATALKAN;
    }

    public function bisaDibatalkan(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public static function daftarStatus(): array
    {
        return self::STATUS_LABEL;
    }

    public static function daftarMetode(): array
    {
        return self::METODE_LABEL;
    }
}