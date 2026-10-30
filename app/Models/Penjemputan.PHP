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

    /**
     * Nama tabel diset manual karena Laravel akan menebak "penjemputans".
     */
    protected $table = 'penjemputan';

    public const STATUS_PENDING   = 'pending';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK   = 'ditolak';
    public const STATUS_SELESAI   = 'selesai';

    /**
     * Penanda alur izin darurat pada kolom metode_verifikasi.
     */
    public const METODE_DARURAT = 'darurat';

    public const STATUS_LABEL = [
        self::STATUS_PENDING   => 'Menunggu',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_DITOLAK   => 'Ditolak',
        self::STATUS_SELESAI   => 'Selesai',
    ];

    /**
     * Warna badge sesuai guideline (dipakai oleh <x-badge tone="...">).
     */
    public const STATUS_TONE = [
        self::STATUS_PENDING   => 'yellow',
        self::STATUS_DISETUJUI => 'mint',
        self::STATUS_DITOLAK   => 'coral',
        self::STATUS_SELESAI   => 'blue',
    ];

    /**
     * Penjelasan singkat untuk orang tua di setiap status.
     */
    public const STATUS_KETERANGAN = [
        self::STATUS_PENDING   => 'Pengajuan sedang diperiksa oleh admin atau pengasuh.',
        self::STATUS_DISETUJUI => 'Penjemput boleh menjemput anak Anda dengan membawa KTP.',
        self::STATUS_DITOLAK   => 'Pengajuan tidak disetujui. Silakan hubungi pihak daycare.',
        self::STATUS_SELESAI   => 'Anak sudah dijemput oleh penjemput ini.',
    ];

    protected $fillable = [
        'anak_id',
        'penjemput_id',
        'user_id',
        'waktu_jemput',
        'metode_verifikasi',
        'foto_bukti',
        'tanda_tangan',
        'catatan',
        'status',
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

    /**
     * Batasi hanya untuk pengajuan yang dibuat oleh orang tua tertentu.
     * Wajib dipakai di semua query halaman orang tua.
     */
    public function scopeMilikOrangTua(Builder $query, int $userId): Builder
    {
        return $query->where($this->qualifyColumn('user_id'), $userId);
    }

    public function scopeDarurat(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('metode_verifikasi'), self::METODE_DARURAT);
    }

    /**
     * Filter berdasarkan status. Nilai kosong atau tidak dikenal diabaikan,
     * sehingga aman dipakai langsung dari input filter di halaman riwayat.
     */
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

    /**
     * Daftar status untuk dropdown/chip filter: [nilai => label].
     */
    public static function daftarStatus(): array
    {
        return self::STATUS_LABEL;
    }
}
