<?php

namespace App\Http\Controllers\Pengasuh;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengasuh\StoreLaporanHarianRequest;
use App\Http\Requests\Pengasuh\UpdateFotoKegiatanRequest;
use App\Http\Requests\Pengasuh\UpdateLaporanHarianRequest;
use App\Models\Anak;
use App\Models\LaporanHarian;
use App\Models\LaporanHarianFoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class LaporanHarianController extends Controller
{
    /**
     * Stage 7 (Lihat): Menampilkan daftar laporan harian anak untuk dipantau & dikelola pengasuh.
     */
    public function index(Request $request): View
    {
        $tanggal = $request->query('tanggal');
        $kondisi = $request->query('kondisi');
        $anakId  = $request->query('anak_id');
        $search  = trim((string) $request->query('q', ''));

        $daftarAnak = Anak::aktif()
            ->with('kelas')
            ->orderBy('nama_lengkap')
            ->get();

        // Statistik ringkas laporan harian
        $statistik = [
            'hari_ini'     => LaporanHarian::whereDate('tanggal', today())->count(),
            'total_semua'  => LaporanHarian::count(),
            'perlu_pantau' => LaporanHarian::whereIn('kondisi', [
                LaporanHarian::KONDISI_KURANG_FIT,
                LaporanHarian::KONDISI_SAKIT,
                LaporanHarian::KONDISI_REWEL,
            ])->whereDate('tanggal', today())->count(),
            'total_foto'   => LaporanHarianFoto::count(),
        ];

        $daftarLaporan = LaporanHarian::query()
            ->with([
                'anak.kelas',
                'anak.user',
                'pengasuh',
                'foto',
            ])
            ->padaTanggal($tanggal)
            ->berstatusKondisi($kondisi)
            ->when($anakId, function ($query) use ($anakId) {
                $query->where('anak_id', (int) $anakId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('anak', function ($qAnak) use ($search) {
                        $qAnak->where('nama_lengkap', 'like', "%{$search}%")
                              ->orWhere('nama_panggilan', 'like', "%{$search}%")
                              ->orWhere('kode_anak', 'like', "%{$search}%");
                    })->orWhere('aktivitas', 'like', "%{$search}%")
                      ->orWhere('makan', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pengasuh.laporan-harian.index', [
            'daftarLaporan' => $daftarLaporan,
            'daftarAnak'    => $daftarAnak,
            'statistik'     => $statistik,
            'tanggalAktif'  => $tanggal,
            'kondisiAktif'  => $kondisi,
            'anakIdAktif'   => $anakId,
            'searchAktif'   => $search,
            'daftarKondisi' => LaporanHarian::daftarKondisi(),
        ]);
    }

    /**
     * Stage 1: Menampilkan form pembuatan laporan harian anak.
     */
    public function create(Request $request): View
    {
        $daftarAnak = Anak::aktif()
            ->with('kelas')
            ->orderBy('nama_lengkap')
            ->get();

        // Daftar ID anak yang sudah memiliki laporan hari ini sebagai informasi cepat bagi pengasuh
        $sudahLaporanHariIniIds = LaporanHarian::whereDate('tanggal', today())
            ->pluck('anak_id')
            ->all();

        $selectedAnakId = $request->query('anak_id')
            ? (int) $request->query('anak_id')
            : ($daftarAnak->first()?->id);

        return view('pengasuh.laporan-harian.create', [
            'daftarAnak'             => $daftarAnak,
            'sudahLaporanHariIniIds' => $sudahLaporanHariIniIds,
            'selectedAnakId'         => $selectedAnakId,
            'daftarKondisi'          => LaporanHarian::daftarKondisi(),
        ]);
    }

    /**
     * Stage 1 & Stage 6: Menyimpan laporan harian baru sekaligus foto kegiatan awal.
     */
    public function store(StoreLaporanHarianRequest $request): RedirectResponse
    {
        $validated      = $request->validated();
        $pengasuhId     = (int) Auth::id();
        $uploadedPaths  = [];

        try {
            if ($request->hasFile('foto_kegiatan')) {
                $keteranganList = $request->input('keterangan_foto', []);

                foreach ($request->file('foto_kegiatan') as $index => $file) {
                    $path = $file->storeAs(
                        LaporanHarianFoto::DIREKTORI_FOTO,
                        $file->hashName(),
                        LaporanHarianFoto::DISK_FOTO
                    );

                    $uploadedPaths[] = [
                        'path_foto'  => $path,
                        'keterangan' => isset($keteranganList[$index]) && trim((string) $keteranganList[$index]) !== ''
                            ? trim((string) $keteranganList[$index])
                            : null,
                    ];
                }
            }

            $laporan = DB::transaction(function () use ($validated, $pengasuhId, $uploadedPaths) {
                $laporan = LaporanHarian::create([
                    'anak_id'          => (int) $validated['anak_id'],
                    'pengasuh_id'      => $pengasuhId,
                    'tanggal'          => $validated['tanggal'],
                    'makan'            => $validated['makan'],
                    'tidur'            => $validated['tidur'],
                    'bab_bak'          => $validated['bab_bak'],
                    'aktivitas'        => $validated['aktivitas'],
                    'kondisi'          => $validated['kondisi'],
                    'catatan_tambahan' => $validated['catatan_tambahan'] ?? null,
                ]);

                if (! empty($uploadedPaths)) {
                    $laporan->foto()->createMany($uploadedPaths);
                }

                return $laporan;
            });
        } catch (Throwable $e) {
            foreach ($uploadedPaths as $item) {
                if (Storage::disk(LaporanHarianFoto::DISK_FOTO)->exists($item['path_foto'])) {
                    Storage::disk(LaporanHarianFoto::DISK_FOTO)->delete($item['path_foto']);
                }
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan laporan harian. Silakan coba kembali.');
        }

        return redirect()
            ->route('pengasuh.laporan-harian.edit', $laporan->id)
            ->with('success', 'Laporan harian anak berhasil dibuat. Anda dapat menambah atau mengelola foto kegiatan di bawah ini.');
    }

    /**
     * Stage 7 & Stage 5/6: Menampilkan form edit laporan harian beserta galeri foto kegiatan.
     */
    public function edit(int $id): View
    {
        $laporan = LaporanHarian::with([
            'anak.kelas',
            'anak.user',
            'pengasuh',
            'foto' => fn ($q) => $q->orderBy('id'),
        ])->findOrFail($id);

        $daftarAnak = Anak::aktif()
            ->with('kelas')
            ->orderBy('nama_lengkap')
            ->get();

        return view('pengasuh.laporan-harian.edit', [
            'laporan'       => $laporan,
            'daftarAnak'    => $daftarAnak,
            'daftarKondisi' => LaporanHarian::daftarKondisi(),
        ]);
    }

    /**
     * Stage 7 (+ opsional Stage 6): Memperbarui laporan harian yang sudah dibuat.
     */
    public function update(UpdateLaporanHarianRequest $request, int $id): RedirectResponse
    {
        $laporan       = LaporanHarian::findOrFail($id);
        $validated     = $request->validated();
        $uploadedPaths = [];

        try {
            if ($request->hasFile('foto_kegiatan')) {
                $keteranganList = $request->input('keterangan_foto', []);

                foreach ($request->file('foto_kegiatan') as $index => $file) {
                    $path = $file->storeAs(
                        LaporanHarianFoto::DIREKTORI_FOTO,
                        $file->hashName(),
                        LaporanHarianFoto::DISK_FOTO
                    );

                    $uploadedPaths[] = [
                        'path_foto'  => $path,
                        'keterangan' => isset($keteranganList[$index]) && trim((string) $keteranganList[$index]) !== ''
                            ? trim((string) $keteranganList[$index])
                            : null,
                    ];
                }
            }

            DB::transaction(function () use ($laporan, $validated, $uploadedPaths) {
                $laporan->update([
                    'anak_id'          => (int) $validated['anak_id'],
                    'tanggal'          => $validated['tanggal'],
                    'makan'            => $validated['makan'],
                    'tidur'            => $validated['tidur'],
                    'bab_bak'          => $validated['bab_bak'],
                    'aktivitas'        => $validated['aktivitas'],
                    'kondisi'          => $validated['kondisi'],
                    'catatan_tambahan' => $validated['catatan_tambahan'] ?? null,
                ]);

                if (! empty($uploadedPaths)) {
                    $laporan->foto()->createMany($uploadedPaths);
                }
            });
        } catch (Throwable $e) {
            foreach ($uploadedPaths as $item) {
                if (Storage::disk(LaporanHarianFoto::DISK_FOTO)->exists($item['path_foto'])) {
                    Storage::disk(LaporanHarianFoto::DISK_FOTO)->delete($item['path_foto']);
                }
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui laporan harian. Silakan periksa kembali isian Anda.');
        }

        return redirect()
            ->route('pengasuh.laporan-harian.edit', $laporan->id)
            ->with('success', 'Perubahan pada laporan harian berhasil disimpan.');
    }

    /**
     * Stage 2: Menghapus laporan harian yang salah beserta seluruh file fotonya.
     */
    public function destroy(int $id): RedirectResponse
    {
        $laporan   = LaporanHarian::with('foto')->findOrFail($id);
        $pathFiles = $laporan->foto->pluck('path_foto')->filter()->all();

        try {
            DB::transaction(function () use ($laporan) {
                $laporan->delete();
            });

            foreach ($pathFiles as $path) {
                if (Storage::disk(LaporanHarianFoto::DISK_FOTO)->exists($path)) {
                    Storage::disk(LaporanHarianFoto::DISK_FOTO)->delete($path);
                }
            }
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Terjadi kesalahan saat menghapus laporan harian.');
        }

        return redirect()
            ->route('pengasuh.laporan-harian.index')
            ->with('success', 'Laporan harian berhasil dihapus.');
    }

    /**
     * Stage 6: Mengunggah foto kegiatan tambahan ke laporan harian yang sudah ada.
     */
    public function storeFoto(Request $request, int $id): RedirectResponse
    {
        $laporan = LaporanHarian::findOrFail($id);

        $request->validate([
            'foto_kegiatan'   => ['required', 'array', 'min:1', 'max:5'],
            'foto_kegiatan.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'keterangan_foto' => ['nullable', 'string', 'max:255'],
        ], [
            'foto_kegiatan.required'  => 'Pilih minimal 1 foto kegiatan untuk diunggah.',
            'foto_kegiatan.max'       => 'Maksimal unggah 5 foto sekaligus.',
            'foto_kegiatan.*.image'   => 'File harus berupa gambar yang valid.',
            'foto_kegiatan.*.mimes'   => 'Format foto hanya mendukung JPG, JPEG, PNG, atau WEBP.',
            'foto_kegiatan.*.max'     => 'Ukuran maksimal masing-masing foto adalah 2 MB.',
        ]);

        $uploadedPaths = [];
        $keterangan    = trim((string) $request->input('keterangan_foto', ''));

        try {
            foreach ($request->file('foto_kegiatan') as $file) {
                $path = $file->storeAs(
                    LaporanHarianFoto::DIREKTORI_FOTO,
                    $file->hashName(),
                    LaporanHarianFoto::DISK_FOTO
                );

                $uploadedPaths[] = [
                    'path_foto'  => $path,
                    'keterangan' => $keterangan !== '' ? $keterangan : null,
                ];
            }

            $laporan->foto()->createMany($uploadedPaths);
        } catch (Throwable $e) {
            foreach ($uploadedPaths as $item) {
                if (Storage::disk(LaporanHarianFoto::DISK_FOTO)->exists($item['path_foto'])) {
                    Storage::disk(LaporanHarianFoto::DISK_FOTO)->delete($item['path_foto']);
                }
            }

            report($e);

            return back()->with('error', 'Gagal mengunggah foto kegiatan.');
        }

        return redirect()
            ->route('pengasuh.laporan-harian.edit', $laporan->id)
            ->with('success', count($uploadedPaths) . ' foto kegiatan berhasil ditambahkan ke laporan harian.');
    }

    /**
     * Stage 5 (Ubah Foto): Mengganti file foto kegiatan yang salah unggah dan/atau keterangannya.
     */
    public function updateFoto(UpdateFotoKegiatanRequest $request, int $laporanId, int $fotoId): RedirectResponse
    {
        $foto = LaporanHarianFoto::where('laporan_harian_id', $laporanId)
            ->findOrFail($fotoId);

        $validated = $request->validated();
        $oldPath   = $foto->path_foto;
        $newPath   = null;

        try {
            if ($request->hasFile('foto_baru')) {
                $file    = $request->file('foto_baru');
                $newPath = $file->storeAs(
                    LaporanHarianFoto::DIREKTORI_FOTO,
                    $file->hashName(),
                    LaporanHarianFoto::DISK_FOTO
                );
            }

            $foto->update([
                'path_foto'  => $newPath ?? $oldPath,
                'keterangan' => array_key_exists('keterangan', $validated)
                    ? (trim((string) $validated['keterangan']) !== '' ? trim((string) $validated['keterangan']) : null)
                    : $foto->keterangan,
            ]);

            if ($newPath && $oldPath && Storage::disk(LaporanHarianFoto::DISK_FOTO)->exists($oldPath)) {
                Storage::disk(LaporanHarianFoto::DISK_FOTO)->delete($oldPath);
            }
        } catch (Throwable $e) {
            if ($newPath && Storage::disk(LaporanHarianFoto::DISK_FOTO)->exists($newPath)) {
                Storage::disk(LaporanHarianFoto::DISK_FOTO)->delete($newPath);
            }

            report($e);

            return back()->with('error', 'Gagal memperbarui foto kegiatan.');
        }

        return redirect()
            ->route('pengasuh.laporan-harian.edit', $laporanId)
            ->with('success', 'Foto kegiatan berhasil diperbarui.');
    }

    /**
     * Stage 5 (Hapus Foto): Menghapus foto kegiatan yang salah unggah.
     */
    public function destroyFoto(int $laporanId, int $fotoId): RedirectResponse
    {
        $foto = LaporanHarianFoto::where('laporan_harian_id', $laporanId)
            ->findOrFail($fotoId);

        $path = $foto->path_foto;

        try {
            $foto->delete();

            if ($path && Storage::disk(LaporanHarianFoto::DISK_FOTO)->exists($path)) {
                Storage::disk(LaporanHarianFoto::DISK_FOTO)->delete($path);
            }
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menghapus foto kegiatan.');
        }

        return redirect()
            ->route('pengasuh.laporan-harian.edit', $laporanId)
            ->with('success', 'Foto kegiatan yang salah unggah telah dihapus.');
    }
}