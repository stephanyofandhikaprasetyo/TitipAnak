<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LaporanHarianFoto extends Model
{
    use HasFactory;

    protected $table = 'laporan_harian_foto';
    protected $guarded = ['id'];

    public const DISK_FOTO      = 'public';
    public const DIREKTORI_FOTO = 'laporan-harian/kegiatan';

    /* ------------------------------------------------------------------
     | Relasi
     | ------------------------------------------------------------------ */

    public function laporanHarian(): BelongsTo
    {
        return $this->belongsTo(LaporanHarian::class, 'laporan_harian_id');
    }

    /* ------------------------------------------------------------------
     | Accessor
     | ------------------------------------------------------------------ */

    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->path_foto
                ? Storage::disk(self::DISK_FOTO)->url($this->path_foto)
                : null,
        );
    }
}