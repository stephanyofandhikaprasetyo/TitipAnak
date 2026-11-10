<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePenjemputanRequest extends FormRequest
{
    /**
     * Izinkan seluruh request pengguna yang sudah terautentikasi.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi input sebelum validasi (membersihkan simbol dari NIK & No HP).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nik'   => preg_replace('/\D/', '', (string) $this->input('nik')),
            'no_hp' => preg_replace('/\D/', '', (string) $this->input('no_hp')),
        ]);
    }

    public function rules(): array
    {
        return [
            // Memastikan anak milik user login & aktif
            'anak_id' => [
                'required',
                'integer',
                Rule::exists('anak', 'id')
                    ->where('user_id', $this->user()?->id ?? auth()->id())
                    ->where('is_active', true)
                    ->whereNull('deleted_at'),
            ],

            'nama_penjemput' => ['required', 'string', 'min:3', 'max:100'],
            'hubungan'       => [
                'required',
                Rule::in([
                    'ayah', 'ibu', 'kakek', 'nenek',
                    'paman', 'bibi', 'kakak', 'saudara',
                    'tetangga', 'lainnya',
                ]),
            ],
            'nik'   => ['required', 'string', 'digits:16'],
            'no_hp' => ['required', 'string', 'digits_between:10,15'],

            'waktu_jemput' => ['required', 'date'],
            'alasan'       => ['required', 'string', 'min:10', 'max:500'],

            // Validasi upload file gambar KTP / identitas yang ramah file JPG/JPEG/PNG/WEBP
            'foto_identitas' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'extensions:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'anak_id.required'        => 'Silakan pilih anak terlebih dahulu.',
            'anak_id.exists'          => 'Data anak tidak valid atau bukan milik Anda.',
            'nama_penjemput.required' => 'Nama penjemput wajib diisi.',
            'nama_penjemput.min'      => 'Nama penjemput minimal 3 karakter.',
            'hubungan.required'       => 'Hubungan dengan anak wajib dipilih.',
            'hubungan.in'             => 'Hubungan yang dipilih tidak valid.',
            'nik.required'            => 'NIK penjemput wajib diisi.',
            'nik.digits'              => 'NIK harus tepat 16 digit angka.',
            'no_hp.required'          => 'No. HP penjemput wajib diisi.',
            'no_hp.digits_between'    => 'No. HP harus 10–15 digit angka.',
            'waktu_jemput.required'   => 'Waktu penjemputan wajib diisi.',
            'waktu_jemput.date'       => 'Format waktu penjemputan tidak valid.',
            'alasan.required'         => 'Alasan / keperluan wajib diisi.',
            'alasan.min'              => 'Alasan minimal 10 karakter.',
            'foto_identitas.required' => 'Foto identitas / KTP wajib diunggah.',
            'foto_identitas.file'     => 'File identitas harus berupa berkas gambar.',
            'foto_identitas.mimes'    => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'foto_identitas.extensions' => 'Ekstensi file harus .jpg, .jpeg, .png, atau .webp.',
            'foto_identitas.max'      => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}