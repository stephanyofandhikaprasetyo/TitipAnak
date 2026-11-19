<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\IzinPenjemputan;
use App\Models\Kehadiran;
use App\Models\PenjemputTerdaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjemputanController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $searchAktif = trim((string) $request->query('q', ''));
        $anakIdAktif = $request->query('anak_id');
        $statusAktif = $request->query('status');

        // Ambil semua anak milik orang tua beserta relasi kelompokUsia, catatanKesehatan, dan penjemputTerdaftar
        $daftarAnak = Anak::with([
                'kelompokUsia',
                'catatanKesehatan',
                'penjemputTerdaftar' => fn ($q) => $q->where('aktif', 1),
            ])
            ->where('orang_tua_id', $userId)
            ->get();

        // Cek kehadiran hari ini untuk menentukan status kehadiran tiap anak
        $kehadiranHariIni = Kehadiran::whereIn('anak_id', $daftarAnak->pluck('id'))
            ->whereDate('tanggal', today())
            ->get()
            ->keyBy('anak_id');

        $daftarAnak->each(function ($anak) use ($kehadiranHariIni) {
            $hadir = $kehadiranHariIni->get($anak->id);
            if ($anak->status !== 'aktif') {
                $anak->status_kehadiran_label = 'Belum Aktif';
                $anak->status_kehadiran_tone  = 'default';
                $anak->bisa_diajukan          = false;
            } elseif ($hadir && $hadir->check_out_pada) {
                $anak->status_kehadiran_label = 'Sudah Dijemput';
                $anak->status_kehadiran_tone  = 'blue';
                $anak->bisa_diajukan          = false;
            } elseif ($hadir) {
                $anak->status_kehadiran_label = 'Hadir di Daycare';
                $anak->status_kehadiran_tone  = 'mint';
                $anak->bisa_diajukan          = true;
            } else {
                $anak->status_kehadiran_label = 'Belum Check-In';
                $anak->status_kehadiran_tone  = 'yellow';
                $anak->bisa_diajukan          = true;
            }
        });

        $anakAktifBisaJemput = $daftarAnak->where('bisa_diajukan', true)->values();

        // ID anak yang masih punya izin berstatus 'menunggu'
        $anakSedangDiajukanIds = IzinPenjemputan::where('diajukan_oleh_id', $userId)
            ->where('status', 'menunggu')
            ->pluck('anak_id')
            ->all();

        // Statistik dari tabel izin_penjemputan
        $baseQuery = IzinPenjemputan::where('diajukan_oleh_id', $userId);
        $statistik = [
            'total'      => (clone $baseQuery)->count(),
            'menunggu'   => (clone $baseQuery)->where('status', 'menunggu')->count(),
            'disetujui'  => (clone $baseQuery)->where('status', 'disetujui')->count(),
            'ditolak'    => (clone $baseQuery)->where('status', 'ditolak')->count(),
            'dibatalkan' => (clone $baseQuery)->where('status', 'dibatalkan')->count(),
        ];

        // Query Riwayat + Filter
        $riwayatPenjemputan = IzinPenjemputan::with(['anak.kelompokUsia', 'validator'])
            ->where('diajukan_oleh_id', $userId)
            ->when($searchAktif !== '', function ($q) use ($searchAktif) {
                $q->where(function ($sub) use ($searchAktif) {
                    $sub->where('nama_penjemput', 'like', "%{$searchAktif}%")
                        ->orWhereHas('anak', fn ($a) => $a->where('nama_lengkap', 'like', "%{$searchAktif}%"));
                });
            })
            ->when($anakIdAktif, fn ($q) => $q->where('anak_id', $anakIdAktif))
            ->when($statusAktif, fn ($q) => $q->where('status', $statusAktif))
            ->orderByDesc('tanggal_jemput')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Daftar Wali Penjemput Terdaftar untuk semua anak milik orang tua ini
        $daftarPenjemput = PenjemputTerdaftar::with('anak')
            ->whereIn('anak_id', $daftarAnak->pluck('id'))
            ->where('aktif', 1)
            ->get();

        $daftarStatus = [
            'menunggu'   => 'Menunggu',
            'disetujui'  => 'Disetujui',
            'ditolak'    => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
        ];

        return view('orangtua.penjemputan.index', compact(
            'daftarAnak',
            'anakAktifBisaJemput',
            'anakSedangDiajukanIds',
            'statistik',
            'riwayatPenjemputan',
            'daftarPenjemput',
            'daftarStatus',
            'searchAktif',
            'anakIdAktif',
            'statusAktif'
        ));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'anak_id'        => ['required', 'exists:anak,id'],
            'mode_penjemput' => ['required', 'in:baru,terdaftar'],
            'penjemput_id'   => ['required_if:mode_penjemput,terdaftar', 'nullable', 'exists:penjemput_terdaftar,id'],
            'nama'           => ['required_if:mode_penjemput,baru', 'nullable', 'string', 'max:100'],
            'hubungan'       => ['required_if:mode_penjemput,baru', 'nullable', 'string', 'max:50'],
            'no_hp'          => ['required_if:mode_penjemput,baru', 'nullable', 'string', 'max:20'],
            'simpan_wali'    => ['nullable', 'boolean'],
            'tanggal_jemput' => ['required', 'date', 'after_or_equal:today'],
            'alasan'         => ['required', 'string', 'max:1000'],
            'foto_bukti'     => ['required_if:mode_penjemput,baru', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Pastikan anak memang milik orang tua yang login
        $anak = Anak::where('id', $validated['anak_id'])
            ->where('orang_tua_id', $userId)
            ->firstOrFail();

        DB::transaction(function () use ($request, $validated, $anak, $userId) {
            $pathFoto = null;
            if ($request->hasFile('foto_bukti')) {
                $pathFoto = $request->file('foto_bukti')->store('penjemput-identitas', 'public');
            }

            if ($validated['mode_penjemput'] === 'terdaftar') {
                $wali = PenjemputTerdaftar::where('id', $validated['penjemput_id'])
                    ->where('anak_id', $anak->id)
                    ->firstOrFail();

                $namaPenjemput = $wali->nama_lengkap;
                $hubungan      = $wali->hubungan;
                $noHp          = $wali->no_hp;
                $fotoFinal     = $pathFoto ?? $wali->path_foto_identitas ?? '';
                $waliId        = $wali->id;
            } else {
                $namaPenjemput = $validated['nama'];
                $hubungan      = $validated['hubungan'];
                $noHp          = $validated['no_hp'];
                $fotoFinal     = $pathFoto ?? '';
                $waliId        = null;

                if ($request->boolean('simpan_wali', true)) {
                    $waliBaru = PenjemputTerdaftar::create([
                        'anak_id'             => $anak->id,
                        'nama_lengkap'        => $namaPenjemput,
                        'hubungan'            => $hubungan,
                        'no_hp'               => $noHp,
                        'path_foto_identitas' => $fotoFinal,
                        'aktif'               => 1,
                    ]);
                    $waliId = $waliBaru->id;
                }
            }

            IzinPenjemputan::create([
                'anak_id'                => $anak->id,
                'diajukan_oleh_id'       => $userId,
                'penjemput_terdaftar_id' => $waliId,
                'nama_penjemput'         => $namaPenjemput,
                'hubungan_penjemput'     => $hubungan,
                'no_hp_penjemput'        => $noHp,
                'path_foto_identitas'    => $fotoFinal,
                'alasan'                 => $validated['alasan'],
                'tanggal_jemput'         => $validated['tanggal_jemput'],
                'status'                 => 'menunggu',
            ]);
        });

        return redirect()
            ->route('orangtua.penjemputan.index')
            ->with('success', 'Pengajuan izin penjemputan berhasil dikirim dan menunggu verifikasi pengasuh.');
    }

    public function cancel(IzinPenjemputan $penjemputan)
    {
        abort_if($penjemputan->diajukan_oleh_id !== Auth::id(), 403);

        if ($penjemputan->status !== 'menunggu') {
            return back()->with('error', 'Hanya pengajuan berstatus menunggu yang dapat dibatalkan.');
        }

        $penjemputan->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Pengajuan penjemputan berhasil dibatalkan.');
    }
}