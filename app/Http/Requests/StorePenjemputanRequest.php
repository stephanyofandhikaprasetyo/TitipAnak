<?php

namespace App\Http\Requests\OrangTua;

use App\Models\Anak;
use App\Models\Penjemput;
use App\Models\Penjemputan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePenjemputanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nik')) {
            $this->merge([
                'nik' => preg_replace('/\D+/', '', (string) $this->input('nik')),
            ]);
        }

        if ($this->has('no_hp')) {
            $this->merge([
                'no_hp' => preg_replace('/[^0-9+]/', '', (string) $this->input('no_hp')),
            ]);
        }
    }

    public function rules(): array
    {
        $userId       = (int) Auth::id();
        $modePenjemput = $this->input('mode_penjemput', 'baru');

        $rules = [
            'anak_id' => [
                'required',
                'integer',
                Rule::exists('anak', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->where('is_active', true)
                          ->whereNull('deleted_at');
                }),
            ],
            'mode_penjemput' => [
                'required',
                'string',
                Rule::in(['terdaftar', 'baru']),
            ],
            'waktu_jemput' => [
                'required',
                'date',
                'after_or_equal:now',
            ],
            'metode_verifikasi' => [
                'required',
                'string',
                Rule::in(array_keys(Penjemputan::daftarMetode())),
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];

        if ($modePenjemput === 'terdaftar') {
            $rules['penjemput_id'] = [
                'required',
                'integer',
                Rule::exists('penjemput', 'id')->where(function ($query) {
                    $query->where('anak_id', (int) $this->input('anak_id'))
                          ->where('is_active', true);
                }),
            ];
            $rules['foto_bukti'] = [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ];
        } else {
            $rules['nama'] = [
                'required',
                'string',
                'min:3',
                'max:100',
            ];
            $rules['hubungan'] = [
                'required',
                'string',
                'max:50',
            ];
            $rules['nik'] = [
                'required',
                'digits:16',
            ];
            $rules['no_hp'] = [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^(\+62|62|0)8[1-9][0-9]{7,11}$/',
            ];
            $rules['foto_bukti'] = [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->has('anak_id')) {
                return;
            }

            $anak = Anak::milikOrangTua((int) Auth::id())->find($this->input('anak_id'));

            if (! $anak || ! $anak->bolehAjukanPenjemputan()) {
                $validator->errors()->add(
                    'anak_id',
                    'Status kehadiran anak saat ini tidak memungkinkan pengajuan penjemputan baru.'
                );
                return;
            }

            $masihAdaPending = Penjemputan::where('anak_id', $anak->id)
                ->whereIn('status', [Penjemputan::STATUS_PENDING, Penjemputan::STATUS_DISETUJUI])
                ->exists();

            if ($masihAdaPending) {
                $validator->errors()->add(
                    'anak_id',
                    'Masih terdapat pengajuan penjemputan aktif (Menunggu/Disetujui) untuk anak ini.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'anak_id.required'             => 'Silakan pilih anak yang akan dijemput.',
            'anak_id.exists'               => 'Data anak tidak ditemukan atau bukan terdaftar atas akun Anda.',
            'mode_penjemput.required'      => 'Pilih tipe penjemput terlebih dahulu.',
            'penjemput_id.required'        => 'Pilih wali penjemput dari daftar penjemput terdaftar.',
            'penjemput_id.exists'          => 'Penjemput yang dipilih tidak aktif atau tidak terdaftar untuk anak ini.',
            'nama.required'                => 'Nama lengkap penjemput wajib diisi.',
            'nama.min'                     => 'Nama penjemput minimal 3 karakter.',
            'hubungan.required'            => 'Hubungan dengan anak wajib diisi (contoh: Ayah, Ibu, Bibi, Nenek).',
            'nik.required'                 => 'NIK KTP penjemput wajib diisi.',
            'nik.digits'                   => 'NIK KTP harus terdiri dari tepat 16 digit angka.',
            'no_hp.required'               => 'Nomor HP penjemput wajib diisi.',
            'no_hp.regex'                  => 'Format nomor HP tidak valid (gunakan awalan 08 atau +628, 10–14 digit).',
            'waktu_jemput.required'        => 'Perkiraan waktu penjemputan wajib diisi.',
            'waktu_jemput.after_or_equal'  => 'Waktu penjemputan tidak boleh di masa lalu.',
            'metode_verifikasi.required'   => 'Pilih metode verifikasi penjemputan.',
            'foto_bukti.required'          => 'Foto identitas/KTP penjemput wajib diunggah untuk penjemput baru.',
            'foto_bukti.image'             => 'File harus berupa gambar yang valid.',
            'foto_bukti.mimes'             => 'Format gambar hanya mendukung JPG, JPEG, PNG, atau WEBP.',
            'foto_bukti.max'               => 'Ukuran maksimal foto identitas adalah 2 MB.',
        ];
    }
}