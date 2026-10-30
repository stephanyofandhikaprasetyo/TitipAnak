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

    /**
     * Nama tabel diset manual karena Laravel akan menebak "anaks".
     */
    protected $table = 'anak';

    public const STATUS_BELUM_DATANG = 'belum_datang';
    public const STATUS_HADIR        = 'hadir';
    public const STATUS_DIJEMPUT     = 'dijemput';
    public const STATUS_PULANG       = 'pulang';

    /**
     * Label status kehadiran (Bahasa Indonesia sederhana untuk orang tua).
     */
    public const STATUS_LABEL = [
        self::STATUS_BELUM_DATANG => 'Belum Datang',
        self::STATUS_HADIR        => 'Hadir',
        self::STATUS_DIJEMPUT     => 'Sedang Dijemput',
        self::STATUS_PULANG       => 'Sudah Pulang',
    ];

    /**
     * Warna badge sesuai guideline (dipakai oleh <x-badge tone="...">).
     */
    public const STATUS_TONE = [
        self::STATUS_BELUM_DATANG => 'ink',
        self::STATUS_HADIR        => 'blue',
        self::STATUS_DIJEMPUT     => 'yellow',
        self::STATUS_PULANG       => 'mint',
    ];

    /**
     * Status kehadiran yang masih boleh diajukan penjemputan daruratnya.
     */
    public const STATUS_BOLEH_AJUKAN = [
        self::STATUS_BELUM_DATANG,
        self::STATUS_HADIR,
    ];

    protected $fillable = [
        'user_id',
        'kode_anak',
        'nama_lengkap',
        'nama_panggilan',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'foto',
        'kelas_id',
        'alamat',
        'catatan_medis',
        'status_kehadiran',
        'jam_masuk',
        'jam_pulang',
        'is_active',
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

    /**
     * Penjemputan terbaru milik anak ini. Aman dipakai dengan eager loading
     * (with('penjemputanTerakhir')) sehingga tidak terjadi N+1 query.
     */
    public function penjemputanTerakhir(): HasOne
    {
        return $this->hasOne(Penjemputan::class)->latestOfMany();
    }

    /* ------------------------------------------------------------------
     | Scope
     | ------------------------------------------------------------------ */

    /**
     * Batasi data hanya untuk anak milik orang tua tertentu.
     * Wajib dipakai di semua query halaman orang tua.
     */
    public function scopeMilikOrangTua(Builder $query, int $userId): Builder
    {
        return $query->where($this->qualifyColumn('user_id'), $userId);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('is_active'), true);
    }

    /**
     * Anak aktif yang masih boleh diajukan penjemputan darurat.
     */
    public function scopeBisaAjukanPenjemputan(Builder $query): Builder
    {
        return $query->aktif()
            ->whereIn($this->qualifyColumn('status_kehadiran'), self::STATUS_BOLEH_AJUKAN);
    }

    /* ------------------------------------------------------------------
     | Accessor
     | ------------------------------------------------------------------ */

    /**
     * URL foto anak, atau null jika belum ada foto (view menampilkan inisial).
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->foto
                ? Storage::disk('public')->url($this->foto)
                : null,
        );
    }

    /**
     * Nama yang ditampilkan: panggilan jika ada, jika tidak nama lengkap.
     */
    protected function namaTampil(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nama_panggilan ?: $this->nama_lengkap,
        );
    }

    /**
     * Satu huruf awal untuk avatar bulat saat foto belum ada.
     */
    protected function inisial(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::upper(Str::substr((string) $this->nama_tampil, 0, 1)),
        );
    }

    /**
     * Umur dalam format sederhana, contoh: "2 tahun 3 bulan" atau "8 bulan".
     */
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

    /**
     * Apakah anak ini masih boleh diajukan penjemputan darurat?
     */
    public function bolehAjukanPenjemputan(): bool
    {
        return $this->is_active
            && in_array($this->status_kehadiran, self::STATUS_BOLEH_AJUKAN, true);
    }
}