<?php

namespace App\Http\Requests\Pengasuh;

use App\Models\LaporanHarian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateLaporanHarianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $laporanId = (int) $this->route('id');

        return [
            'anak_id' => [
                'required',
                'integer',
                Rule::exists('anak', 'id')->where(function ($query) {
                    $query->where('is_active', true)
                          ->whereNull('deleted_at');
                }),
            ],
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('laporan_harian', 'tanggal')
                    ->ignore($laporanId)
                    ->where(function ($query) {
                        return $query->where('anak_id', (int) $this->input('anak_id'));
                    }),
            ],
            'makan' => [
                'required',
                'string',
                'min:3',
                'max:2000',
            ],
            'tidur' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'bab_bak' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'aktivitas' => [
                'required',
                'string',
                'min:3',
                'max:2000',
            ],
            'kondisi' => [
                'required',
                'string',
                Rule::in(array_keys(LaporanHarian::daftarKondisi())),
            ],
            'catatan_tambahan' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'foto_kegiatan' => [
                'nullable',
                'array',
                'max:5',
            ],
            'foto_kegiatan.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'keterangan_foto' => [
                'nullable',
                'array',
            ],
            'keterangan_foto.*' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'anak_id.required'          => 'Silakan pilih anak.',
            'anak_id.exists'            => 'Data anak tidak ditemukan atau berstatus tidak aktif.',
            'tanggal.required'          => 'Tanggal laporan wajib diisi.',
            'tanggal.before_or_equal'   => 'Tanggal laporan tidak boleh melebihi hari ini.',
            'tanggal.unique'            => 'Sudah ada laporan harian lain untuk anak ini pada tanggal tersebut.',
            'makan.required'            => 'Catatan konsumsi makan/minum wajib diisi.',
            'tidur.required'            => 'Catatan tidur anak wajib diisi.',
            'bab_bak.required'          => 'Catatan BAB/BAK wajib diisi.',
            'aktivitas.required'        => 'Catatan aktivitas anak wajib diisi.',
            'kondisi.required'          => 'Pilih kondisi umum anak.',
            'foto_kegiatan.max'         => 'Maksimal unggah 5 foto baru sekaligus.',
            'foto_kegiatan.*.image'     => 'File harus berupa gambar yang valid.',
            'foto_kegiatan.*.mimes'     => 'Format gambar hanya mendukung JPG, JPEG, PNG, atau WEBP.',
            'foto_kegiatan.*.max'       => 'Ukuran maksimal setiap foto adalah 2 MB.',
        ];
    }
}