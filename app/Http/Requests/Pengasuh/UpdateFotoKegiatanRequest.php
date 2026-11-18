<?php

namespace App\Http\Requests\Pengasuh;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateFotoKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'foto_baru' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'foto_baru.image' => 'File pengganti harus berupa gambar yang valid.',
            'foto_baru.mimes' => 'Format foto pengganti hanya mendukung JPG, JPEG, PNG, atau WEBP.',
            'foto_baru.max'   => 'Ukuran foto pengganti maksimal 2 MB.',
            'keterangan.max'  => 'Keterangan foto maksimal 255 karakter.',
        ];
    }
}