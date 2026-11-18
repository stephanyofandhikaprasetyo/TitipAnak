<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrangTua\StorePenjemputanRequest;
use App\Models\Anak;
use App\Models\Penjemput;
use App\Models\Penjemputan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PenjemputanController extends Controller
{
    /**
     * Menampilkan daftar riwayat & status penjemputan anak milik orang tua yang login.
     */
    public function index(Request $request): View
    {
        $userId = (int) Auth::id();
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));
        $anakId = $request->query('anak_id');

        // Daftar anak milik orang tua untuk filter & pengecekan tombol ajukan
        $daftarAnak = Anak::milikOrangTua($userId)
            ->aktif()
            ->with(['kelas', 'penjemput' => fn ($q) => $q->aktif()->orderBy('nama')])
            ->orderBy('nama_lengkap')
            ->get();

        // Statistik ringkas untuk kartu dashboard penjemputan
        $statistikBase = Penjemputan::milikOrangTua($userId);
        $statistik = [
            'total'     => (clone $statistikBase)->count(),
            'menunggu'  => (clone $statistikBase)->where('status', Penjemputan::STATUS_PENDING)->count(),
            'disetujui' => (clone $statistikBase)->where('status', Penjemputan::STATUS_DISETUJUI)->count(),
            'selesai'   => (clone $statistikBase)->where('status', Penjemputan::STATUS_SELESAI)->count(),
        ];

        // Query utama riwayat penjemputan dengan eager loading
        $riwayatPenjemputan = Penjemputan::query()
            ->milikOrangTua($userId)
            ->with([
                'anak.kelas',
                'penjemput',
            ])
            ->berstatus($status)
            ->when($anakId, function ($query) use ($anakId) {
                $query->where('anak_id', (int) $anakId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('anak', function ($qAnak) use ($search) {
                        $qAnak->where('nama_lengkap', 'like', "%{$search}%")
                              ->orWhere('nama_panggilan', 'like', "%{$search}%")
                              ->orWhere('kode_anak', 'like', "%{$search}%");
                    })->orWhereHas('penjemput', function ($qPenjemput) use ($search) {
                        $qPenjemput->where('nama', 'like', "%{$search}%")
                                   ->orWhere('hubungan', 'like', "%{$search}%");
                    });
                });
            })
            ->orderByDesc('waktu_jemput')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Daftar penjemput aktif milik semua anak orang tua ini
        $daftarPenjemput = Penjemput::milikOrangTua($userId)
            ->aktif()
            ->with('anak')
            ->orderBy('nama')
            ->get();

        return view('orangtua.penjemputan.index', [
            'daftarAnak'         => $daftarAnak,
            'riwayatPenjemputan' => $riwayatPenjemputan,
            'daftarPenjemput'    => $daftarPenjemput,
            'statistik'          => $statistik,
            'statusAktif'        => $status,
            'searchAktif'        => $search,
            'anakIdAktif'        => $anakId,
            'daftarStatus'       => Penjemputan::daftarStatus(),
        ]);
    }

    /**
     * Menampilkan form pengajuan penjemputan baru oleh orang tua.
     */
    public function create(Request $request): View
    {
        $userId = (int) Auth::id();

        $daftarAnak = Anak::milikOrangTua($userId)
            ->bisaAjukanPenjemputan()
            ->with([
                'kelas',
                'penjemput' => fn ($q) => $q->aktif()->orderBy('nama'),
            ])
            ->orderBy('nama_lengkap')
            ->get();

        // Ambil daftar ID anak yang sedang memiliki pengajuan aktif (pending/disetujui)
        $anakSedangDiajukanIds = Penjemputan::milikOrangTua($userId)
            ->whereIn('status', [Penjemputan::STATUS_PENDING, Penjemputan::STATUS_DISETUJUI])
            ->pluck('anak_id')
            ->unique()
            ->all();

        $selectedAnakId = $request->query('anak_id')
            ? (int) $request->query('anak_id')
            : ($daftarAnak->first()?->id);

        return view('orangtua.penjemputan.create', [
            'daftarAnak'            => $daftarAnak,
            'anakSedangDiajukanIds' => $anakSedangDiajukanIds,
            'selectedAnakId'        => $selectedAnakId,
            'daftarMetode'          => Penjemputan::daftarMetode(),
        ]);
    }

    /**
     * Menyimpan pengajuan penjemputan baru (sekaligus mendaftarkan penjemput jika mode 'baru').
     */
    public function store(StorePenjemputanRequest $request): RedirectResponse
    {
        $userId    = (int) Auth::id();
        $validated = $request->validated();
        $pathFoto  = null;

        try {
            if ($request->hasFile('foto_bukti')) {
                $file     = $request->file('foto_bukti');
                $filename = $file->hashName();
                $pathFoto = $file->storeAs(
                    Penjemputan::DIREKTORI_FOTO,
                    $filename,
                    Penjemputan::DISK_FOTO
                );
            }

            DB::transaction(function () use ($validated, $userId, $pathFoto) {
                $anak = Anak::milikOrangTua($userId)
                    ->lockForUpdate()
                    ->findOrFail((int) $validated['anak_id']);

                if ($validated['mode_penjemput'] === 'terdaftar') {
                    $penjemput = Penjemput::where('anak_id', $anak->id)
                        ->aktif()
                        ->findOrFail((int) $validated['penjemput_id']);

                    // Gunakan foto bukti baru jika diunggah; jika tidak, pakai foto identitas terdaftar
                    $fotoBuktiFinal = $pathFoto ?? $penjemput->foto;
                } else {
                    $penjemput = Penjemput::create([
                        'anak_id'   => $anak->id,
                        'nama'      => $validated['nama'],
                        'hubungan'  => $validated['hubungan'],
                        'nik'       => $validated['nik'],
                        'no_hp'     => $validated['no_hp'],
                        'foto'      => $pathFoto,
                        'is_active' => true,
                    ]);

                    $fotoBuktiFinal = $pathFoto;
                }

                Penjemputan::create([
                    'anak_id'           => $anak->id,
                    'penjemput_id'      => $penjemput->id,
                    'user_id'           => $userId,
                    'waktu_jemput'      => $validated['waktu_jemput'],
                    'metode_verifikasi' => $validated['metode_verifikasi'],
                    'foto_bukti'        => $fotoBuktiFinal,
                    'catatan'           => $validated['catatan'] ?? null,
                    'status'            => Penjemputan::STATUS_PENDING,
                ]);
            });
        } catch (Throwable $e) {
            if ($pathFoto && Storage::disk(Penjemputan::DISK_FOTO)->exists($pathFoto)) {
                Storage::disk(Penjemputan::DISK_FOTO)->delete($pathFoto);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan penjemputan. Silakan coba kembali.');
        }

        return redirect()
            ->route('orangtua.penjemputan.index')
            ->with('success', 'Pengajuan penjemputan berhasil dikirim dan sedang menunggu verifikasi pihak daycare.');
    }

    /**
     * Membatalkan pengajuan penjemputan yang masih berstatus pending.
     */
    public function cancel(int $id): RedirectResponse
    {
        $userId = (int) Auth::id();

        $penjemputan = Penjemputan::milikOrangTua($userId)->findOrFail($id);

        if (! $penjemputan->bisaDibatalkan()) {
            return back()->with(
                'error',
                'Pengajuan penjemputan tidak dapat dibatalkan karena statusnya sudah ' . strtolower($penjemputan->status_label) . '.'
            );
        }

        $penjemputan->update([
            'status' => Penjemputan::STATUS_DIBATALKAN,
        ]);

        return redirect()
            ->route('orangtua.penjemputan.index')
            ->with('success', 'Pengajuan penjemputan berhasil dibatalkan.');
    }

    /**
     * Menyajikan file foto identitas/bukti penjemput secara aman hanya untuk pemilik data.
     */
    public function foto(int $id): StreamedResponse
    {
        $userId = (int) Auth::id();

        $penjemputan = Penjemputan::milikOrangTua($userId)
            ->with('penjemput')
            ->findOrFail($id);

        $path = $penjemputan->foto_bukti ?: $penjemputan->penjemput?->foto;

        abort_if(blank($path), 404, 'Foto bukti penjemput tidak tersedia.');

        if (Storage::disk(Penjemputan::DISK_FOTO)->exists($path)) {
            return Storage::disk(Penjemputan::DISK_FOTO)->response($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        abort(404, 'File foto tidak ditemukan di penyimpanan.');
    }
}