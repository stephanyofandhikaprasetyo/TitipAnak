<?php

namespace App\Http\Requests\Pengasuh;

use App\Models\LaporanHarian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreLaporanHarianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
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
                Rule::unique('laporan_harian', 'tanggal')->where(function ($query) {
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
            'anak_id.required'          => 'Silakan pilih anak yang akan dibuatkan laporan harian.',
            'anak_id.exists'            => 'Data anak tidak ditemukan atau berstatus tidak aktif.',
            'tanggal.required'          => 'Tanggal laporan wajib diisi.',
            'tanggal.date'              => 'Format tanggal laporan tidak valid.',
            'tanggal.before_or_equal'   => 'Tanggal laporan tidak boleh melebihi hari ini.',
            'tanggal.unique'            => 'Laporan harian untuk anak ini pada tanggal tersebut sudah dibuat. Silakan ubah laporan yang sudah ada.',
            'makan.required'            => 'Catatan konsumsi makan/minum anak wajib diisi.',
            'makan.min'                 => 'Catatan makan minimal 3 karakter.',
            'tidur.required'            => 'Catatan jam/durasi tidur anak wajib diisi.',
            'tidur.min'                 => 'Catatan tidur minimal 3 karakter.',
            'bab_bak.required'          => 'Catatan frekuensi/kondisi BAB & BAK wajib diisi.',
            'bab_bak.min'               => 'Catatan BAB/BAK minimal 3 karakter.',
            'aktivitas.required'        => 'Catatan aktivitas anak hari ini wajib diisi.',
            'aktivitas.min'             => 'Catatan aktivitas minimal 3 karakter.',
            'kondisi.required'          => 'Pilih kondisi umum anak hari ini.',
            'kondisi.in'                => 'Pilihan kondisi anak tidak valid.',
            'foto_kegiatan.max'         => 'Maksimal unggah 5 foto kegiatan dalam satu kali pengiriman.',
            'foto_kegiatan.*.image'     => 'Setiap file foto kegiatan harus berupa gambar yang valid.',
            'foto_kegiatan.*.mimes'     => 'Format foto kegiatan hanya mendukung JPG, JPEG, PNG, atau WEBP.',
            'foto_kegiatan.*.max'       => 'Ukuran masing-masing foto kegiatan maksimal 2 MB.',
        ];
    }
}