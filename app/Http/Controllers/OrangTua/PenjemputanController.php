<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenjemputanRequest;
use App\Models\Anak;
use App\Models\Penjemput;
use App\Models\Penjemputan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class PenjemputanController extends Controller
{
    /**
     * Tampilkan form pengajuan izin penjemputan.
     */
    public function create(): View
    {
        $anakList = Anak::query()
            ->where('user_id', 1) // Bypass Auth: diganti ID 1
            ->where('is_active', true)
            ->with('kelas:id,nama_kelas')
            ->orderBy('nama_lengkap')
            ->get(['id', 'kode_anak', 'nama_lengkap', 'kelas_id']);

        return view('orangtua.penjemputan.create', compact('anakList'));
    }

    /**
     * Simpan pengajuan izin penjemputan.
     */
    public function store(StorePenjemputanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Lapisan pertahanan kedua: pastikan anak milik user dengan ID 1
        $anak = Anak::query()
            ->where('id', $validated['anak_id'])
            ->where('user_id', 1) // Bypass Auth: diganti ID 1
            ->firstOrFail();

        $fotoPath = null;

        try {
            DB::transaction(function () use ($request, $validated, $anak, &$fotoPath) {
                // Simpan foto identitas ke disk privat 'local'
                $fotoPath = $request->file('foto_identitas')
                    ->store('penjemputan/identitas', 'local');

                // 1) Buat record penjemput
                $penjemput = Penjemput::create([
                    'anak_id'   => $anak->id,
                    'nama'      => $validated['nama_penjemput'],
                    'hubungan'  => $validated['hubungan'],
                    'nik'       => $validated['nik'],
                    'no_hp'     => $validated['no_hp'],
                    'foto'      => $fotoPath,
                    'is_active' => true,
                ]);

                // 2) Buat record pengajuan penjemputan
                Penjemputan::create([
                    'anak_id'           => $anak->id,
                    'penjemput_id'      => $penjemput->id,
                    'user_id'           => 1, // Bypass Auth: diganti ID 1
                    'waktu_jemput'      => $validated['waktu_jemput'],
                    'metode_verifikasi' => 'ktp',
                    'foto_bukti'        => $fotoPath,
                    'catatan'           => $validated['alasan'],
                    'status'            => 'pending',
                ]);
            });
        } catch (Throwable $e) {
            // Bersihkan file jika transaksi gagal
            if ($fotoPath && Storage::disk('local')->exists($fotoPath)) {
                Storage::disk('local')->delete($fotoPath);
            }

            report($e);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan saat menyimpan pengajuan. Silakan coba lagi.']);
        }

        return redirect()
            ->route('orangtua.penjemputan.create')
            ->with('success', 'Pengajuan izin penjemputan berhasil dikirim dan menunggu verifikasi.');
    }
}