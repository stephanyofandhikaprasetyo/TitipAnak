<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IzinPenjemputan extends Model
{
    protected $table = 'izin_penjemputan';

    protected $fillable = [
        'anak_id',
        'diajukan_oleh_id',
        'penjemput_terdaftar_id',
        'nama_penjemput',
        'hubungan_penjemput',
        'no_hp_penjemput',
        'path_foto_identitas',
        'alasan',
        'tanggal_jemput',
        'status',
        'divalidasi_oleh_id',
        'divalidasi_pada',
    ];

    protected $casts = [
        'tanggal_jemput'  => 'date',
        'divalidasi_pada' => 'datetime',
    ];

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class, 'anak_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh_id');
    }

    public function penjemputTerdaftar(): BelongsTo
    {
        return $this->belongsTo(PenjemputTerdaftar::class, 'penjemput_terdaftar_id');
    }
}