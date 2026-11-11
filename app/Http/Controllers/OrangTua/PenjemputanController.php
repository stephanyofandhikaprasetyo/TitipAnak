<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenjemputanRequest;
use App\Models\Anak;
use App\Models\Penjemput;
use App\Models\Penjemputan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class PenjemputanController extends Controller
{
    /**
     * Pastikan User 1 otomatis terautentikasi untuk pengujian lokal
     */
    private function ensureAuthenticated(): void
    {
        if (!Auth::check()) {
            Auth::loginUsingId(1);
        }
    }

    /**
     * Menampilkan daftar status dan riwayat pengajuan izin penjemputan milik Orang Tua.
     */
    public function index()
    {
        $this->ensureAuthenticated();

        $userId = Auth::id();

        $penjemputanList = Penjemputan::with(['anak.kelas', 'penjemput'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('orangtua.penjemputan.index', compact('penjemputanList'));
    }

    /**
     * Tampilkan form pengajuan izin penjemputan.
     */
    public function create(): View
    {
        $this->ensureAuthenticated();

        $anakList = Anak::query()
            ->where('user_id', Auth::id())
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
        $this->ensureAuthenticated();

        $validated = $request->validated();

        $anak = Anak::query()
            ->where('id', $validated['anak_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $fotoPath = null;

        try {
            DB::transaction(function () use ($request, $validated, $anak, &$fotoPath) {
                $fotoPath = $request->file('foto_identitas')
                    ->store('penjemputan/identitas', 'local');

                $penjemput = Penjemput::create([
                    'anak_id'   => $anak->id,
                    'nama'      => $validated['nama_penjemput'],
                    'hubungan'  => $validated['hubungan'],
                    'nik'       => $validated['nik'],
                    'no_hp'     => $validated['no_hp'],
                    'foto'      => $fotoPath,
                    'is_active' => true,
                ]);

                Penjemputan::create([
                    'anak_id'           => $anak->id,
                    'penjemput_id'      => $penjemput->id,
                    'user_id'           => Auth::id(),
                    'waktu_jemput'      => $validated['waktu_jemput'],
                    'metode_verifikasi' => 'ktp',
                    'foto_bukti'        => $fotoPath,
                    'catatan'           => $validated['alasan'],
                    'status'            => 'pending',
                ]);
            });
        } catch (Throwable $e) {
            if ($fotoPath && Storage::disk('local')->exists($fotoPath)) {
                Storage::disk('local')->delete($fotoPath);
            }

            report($e);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage()]);
        }

        return redirect()
            ->route('orangtua.penjemputan.index')
            ->with('success', 'Pengajuan izin penjemputan berhasil dikirim dan menunggu verifikasi.');
    }

    /**
     * Membatalkan pengajuan izin penjemputan (Status 'pending' atau 'disetujui').
     */
    public function cancel($id): RedirectResponse
    {
        $this->ensureAuthenticated();

        $penjemputan = Penjemputan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Validasi: Pembatalan diperbolehkan jika status masih 'pending' atau 'disetujui'
        if (in_array($penjemputan->status, ['selesai', 'ditolak', 'dibatalkan'])) {
            return redirect()->back()->with('error', 'Pengajuan ini tidak dapat dibatalkan karena sudah selesai atau ditolak.');
        }

        // Update status di database
        $penjemputan->update([
            'status' => 'dibatalkan',
        ]);

        return redirect()->route('orangtua.penjemputan.index')
            ->with('success', 'Pengajuan izin penjemputan berhasil dibatalkan.');
    }
}