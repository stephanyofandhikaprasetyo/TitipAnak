<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Catatan kesehatan anak (satu per anak). Salah satu syarat kelengkapan pendaftaran. */
#[Fillable(['anak_id', 'riwayat_medis', 'alergi', 'pantangan_makanan', 'catatan'])]
class CatatanKesehatan extends Model
{
    protected $table = 'catatan_kesehatan';

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class);
    }

    /** Ringkasan singkat alergi, pantangan makanan, riwayat medis, dan catatan. */
    public function ringkasan(): string
    {
        return collect([
            $this->alergi ? "Alergi: {$this->alergi}" : null,
            $this->pantangan_makanan ? "Pantangan: {$this->pantangan_makanan}" : null,
            $this->riwayat_medis ? "Riwayat: {$this->riwayat_medis}" : null,
            $this->catatan,
        ])->filter()->implode(' · ');
    }
}