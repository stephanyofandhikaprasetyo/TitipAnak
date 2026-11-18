<?php

namespace App\Http\Controllers\Pengasuh;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengasuh\StoreLaporanInsidenRequest;
use App\Http\Requests\Pengasuh\UpdateLaporanInsidenRequest;
use App\Models\Anak;
use App\Models\LaporanInsiden;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class LaporanInsidenController extends Controller
{
    /**
     * Menampilkan daftar seluruh laporan insiden anak untuk dipantau & diperbarui pengasuh.
     */
    public function index(Request $request): View
    {
        $status    = $request->query('status');
        $keparahan = $request->query('keparahan');
        $anakId    = $request->query('anak_id');
        $search    = trim((string) $request->query('q', ''));

        $daftarAnak = Anak::aktif()
            ->with('kelas')
            ->orderBy('nama_lengkap')
            ->get();

        $statistik = [
            'total'      => LaporanInsiden::count(),
            'ditangani'  => LaporanInsiden::where('status_penanganan', LaporanInsiden::STATUS_DITANGANI)->count(),
            'pemantauan' => LaporanInsiden::where('status_penanganan', LaporanInsiden::STATUS_PEMANTAUAN)->count(),
            'selesai'    => LaporanInsiden::where('status_penanganan', LaporanInsiden::STATUS_SELESAI)->count(),
        ];

        $daftarInsiden = LaporanInsiden::query()
            ->with([
                'anak.kelas',
                'anak.user',
                'pengasuh',
            ])
            ->berstatusPenanganan($status)
            ->berkeparahan($keparahan)
            ->when($anakId, function ($query) use ($anakId) {
                $query->where('anak_id', (int) $anakId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('judul_insiden', 'like', "%{$search}%")
                        ->orWhere('kronologi', 'like', "%{$search}%")
                        ->orWhereHas('anak', function ($qAnak) use ($search) {
                            $qAnak->where('nama_lengkap', 'like', "%{$search}%")
                                  ->orWhere('nama_panggilan', 'like', "%{$search}%")
                                  ->orWhere('kode_anak', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('waktu_kejadian')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pengasuh.laporan-insiden.index', [
            'daftarInsiden'   => $daftarInsiden,
            'daftarAnak'      => $daftarAnak,
            'statistik'       => $statistik,
            'statusAktif'     => $status,
            'keparahanAktif'  => $keparahan,
            'anakIdAktif'     => $anakId,
            'searchAktif'     => $search,
            'daftarStatus'    => LaporanInsiden::daftarStatus(),
            'daftarKeparahan' => LaporanInsiden::daftarKeparahan(),
        ]);
    }

    /**
     * Stage 4: Menampilkan form pencatatan laporan insiden baru.
     */
    public function create(Request $request): View
    {
        $daftarAnak = Anak::aktif()
            ->with('kelas')
            ->orderBy('nama_lengkap')
            ->get();

        $selectedAnakId = $request->query('anak_id')
            ? (int) $request->query('anak_id')
            : ($daftarAnak->first()?->id);

        return view('pengasuh.laporan-insiden.create', [
            'daftarAnak'      => $daftarAnak,
            'selectedAnakId'  => $selectedAnakId,
            'daftarKeparahan' => LaporanInsiden::daftarKeparahan(),
            'daftarStatus'    => LaporanInsiden::daftarStatus(),
        ]);
    }

    /**
     * Stage 4: Menyimpan catatan laporan insiden baru agar terdokumentasi.
     */
    public function store(StoreLaporanInsidenRequest $request): RedirectResponse
    {
        $validated  = $request->validated();
        $pengasuhId = (int) Auth::id();
        $pathFoto   = null;

        try {
            if ($request->hasFile('foto_bukti')) {
                $file     = $request->file('foto_bukti');
                $pathFoto = $file->storeAs(
                    LaporanInsiden::DIREKTORI_FOTO,
                    $file->hashName(),
                    LaporanInsiden::DISK_FOTO
                );
            }

            $insiden = LaporanInsiden::create([
                'anak_id'              => (int) $validated['anak_id'],
                'pengasuh_id'          => $pengasuhId,
                'waktu_kejadian'       => $validated['waktu_kejadian'],
                'judul_insiden'        => $validated['judul_insiden'],
                'tingkat_keparahan'    => $validated['tingkat_keparahan'],
                'kronologi'            => $validated['kronologi'],
                'tindakan_pertolongan' => $validated['tindakan_pertolongan'],
                'foto_bukti'           => $pathFoto,
                'status_penanganan'    => $validated['status_penanganan'],
            ]);
        } catch (Throwable $e) {
            if ($pathFoto && Storage::disk(LaporanInsiden::DISK_FOTO)->exists($pathFoto)) {
                Storage::disk(LaporanInsiden::DISK_FOTO)->delete($pathFoto);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan laporan insiden. Silakan coba kembali.');
        }

        return redirect()
            ->route('pengasuh.laporan-insiden.index')
            ->with('success', 'Laporan insiden berhasil dicatat dan terdokumentasi.');
    }

    /**
     * Stage 3: Menampilkan form ubah laporan insiden untuk menambahkan informasi terbaru.
     */
    public function edit(int $id): View
    {
        $insiden = LaporanInsiden::with(['anak.kelas', 'anak.user', 'pengasuh'])
            ->findOrFail($id);

        $daftarAnak = Anak::aktif()
            ->with('kelas')
            ->orderBy('nama_lengkap')
            ->get();

        return view('pengasuh.laporan-insiden.edit', [
            'insiden'         => $insiden,
            'daftarAnak'      => $daftarAnak,
            'daftarKeparahan' => LaporanInsiden::daftarKeparahan(),
            'daftarStatus'    => LaporanInsiden::daftarStatus(),
        ]);
    }

    /**
     * Stage 3: Memperbarui laporan insiden jika terdapat informasi tambahan atau perubahan status.
     */
    public function update(UpdateLaporanInsidenRequest $request, int $id): RedirectResponse
    {
        $insiden   = LaporanInsiden::findOrFail($id);
        $validated = $request->validated();
        $oldFoto   = $insiden->foto_bukti;
        $newFoto   = null;
        $hapusFoto = (bool) ($validated['hapus_foto_bukti'] ?? false);

        try {
            if ($request->hasFile('foto_bukti')) {
                $file    = $request->file('foto_bukti');
                $newFoto = $file->storeAs(
                    LaporanInsiden::DIREKTORI_FOTO,
                    $file->hashName(),
                    LaporanInsiden::DISK_FOTO
                );
            }

            $fotoFinal = $oldFoto;
            if ($newFoto) {
                $fotoFinal = $newFoto;
            } elseif ($hapusFoto) {
                $fotoFinal = null;
            }

            DB::transaction(function () use ($insiden, $validated, $fotoFinal) {
                $insiden->update([
                    'anak_id'              => (int) $validated['anak_id'],
                    'waktu_kejadian'       => $validated['waktu_kejadian'],
                    'judul_insiden'        => $validated['judul_insiden'],
                    'tingkat_keparahan'    => $validated['tingkat_keparahan'],
                    'kronologi'            => $validated['kronologi'],
                    'tindakan_pertolongan' => $validated['tindakan_pertolongan'],
                    'foto_bukti'           => $fotoFinal,
                    'status_penanganan'    => $validated['status_penanganan'],
                ]);
            });

            if (($newFoto || $hapusFoto) && $oldFoto && Storage::disk(LaporanInsiden::DISK_FOTO)->exists($oldFoto)) {
                Storage::disk(LaporanInsiden::DISK_FOTO)->delete($oldFoto);
            }
        } catch (Throwable $e) {
            if ($newFoto && Storage::disk(LaporanInsiden::DISK_FOTO)->exists($newFoto)) {
                Storage::disk(LaporanInsiden::DISK_FOTO)->delete($newFoto);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui laporan insiden. Silakan coba kembali.');
        }

        return redirect()
            ->route('pengasuh.laporan-insiden.index')
            ->with('success', 'Laporan insiden berhasil diperbarui.');
    }

    /**
     * Menghapus laporan insiden beserta file foto bukti jika ada.
     */
    public function destroy(int $id): RedirectResponse
    {
        $insiden  = LaporanInsiden::findOrFail($id);
        $fotoPath = $insiden->foto_bukti;

        try {
            $insiden->delete();

            if ($fotoPath && Storage::disk(LaporanInsiden::DISK_FOTO)->exists($fotoPath)) {
                Storage::disk(LaporanInsiden::DISK_FOTO)->delete($fotoPath);
            }
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Terjadi kesalahan saat menghapus laporan insiden.');
        }

        return redirect()
            ->route('pengasuh.laporan-insiden.index')
            ->with('success', 'Laporan insiden berhasil dihapus.');
    }
}