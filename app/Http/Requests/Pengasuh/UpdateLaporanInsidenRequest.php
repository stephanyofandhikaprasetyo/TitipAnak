<?php

namespace App\Http\Requests\Pengasuh;

use App\Models\LaporanInsiden;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateLaporanInsidenRequest extends FormRequest
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
            'waktu_kejadian' => [
                'required',
                'date',
                'before_or_equal:now',
            ],
            'judul_insiden' => [
                'required',
                'string',
                'min:4',
                'max:150',
            ],
            'tingkat_keparahan' => [
                'required',
                'string',
                Rule::in(array_keys(LaporanInsiden::daftarKeparahan())),
            ],
            'kronologi' => [
                'required',
                'string',
                'min:10',
                'max:3000',
            ],
            'tindakan_pertolongan' => [
                'required',
                'string',
                'min:5',
                'max:3000',
            ],
            'status_penanganan' => [
                'required',
                'string',
                Rule::in(array_keys(LaporanInsiden::daftarStatus())),
            ],
            'foto_bukti' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'hapus_foto_bukti' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'anak_id.required'              => 'Silakan pilih anak.',
            'waktu_kejadian.required'       => 'Waktu kejadian insiden wajib diisi.',
            'waktu_kejadian.before_or_equal'=> 'Waktu kejadian tidak boleh di masa depan.',
            'judul_insiden.required'        => 'Judul singkat insiden wajib diisi.',
            'tingkat_keparahan.required'    => 'Pilih tingkat keparahan insiden.',
            'kronologi.required'            => 'Kronologi kejadian wajib diisi.',
            'tindakan_pertolongan.required' => 'Tindakan pertolongan wajib diisi.',
            'status_penanganan.required'    => 'Pilih status penanganan.',
            'foto_bukti.image'              => 'File bukti harus berupa gambar yang valid.',
            'foto_bukti.mimes'              => 'Format foto bukti hanya mendukung JPG, JPEG, PNG, atau WEBP.',
            'foto_bukti.max'                => 'Ukuran foto bukti maksimal 2 MB.',
        ];
    }
}