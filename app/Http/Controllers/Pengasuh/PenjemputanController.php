<?php

namespace App\Http\Controllers\Pengasuh;

use App\Http\Controllers\Controller;
use App\Models\Penjemputan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenjemputanController extends Controller
{
    /**
     * PBI-17d: Melihat daftar izin penjemputan yang masuk (semua anak).
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');

        $query = Penjemputan::with(['anak.kelas', 'penjemput', 'user'])
            ->latest();

        if ($statusFilter && in_array($statusFilter, ['pending', 'disetujui', 'ditolak', 'selesai'])) {
            $query->where('status', $statusFilter);
        }

        $penjemputanList = $query->paginate(10)->withQueryString();

        // Statistik ringkas untuk kartu atas
        $stats = [
            'pending'   => Penjemputan::where('status', 'pending')->count(),
            'disetujui' => Penjemputan::where('status', 'disetujui')->count(),
            'selesai'   => Penjemputan::where('status', 'selesai')->count(),
        ];

        return view('pengasuh.penjemputan.index', compact('penjemputanList', 'stats', 'statusFilter'));
    }

    /**
     * PBI-17e: Memvalidasi izin penjemputan (Setujui / Tolak / Selesai).
     */
    public function validasi(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'status'  => ['required', 'in:disetujui,ditolak,selesai'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $penjemputan = Penjemputan::with('anak')->findOrFail($id);

        $penjemputan->update([
            'status'  => $validated['status'],
            'catatan' => $validated['catatan'] ?? $penjemputan->catatan,
        ]);

        // Jika penyerahan anak selesai, update status_kehadiran anak menjadi 'dijemput'
        if ($validated['status'] === 'selesai' && $penjemputan->anak) {
            $penjemputan->anak->update([
                'status_kehadiran' => 'dijemput',
                'jam_pulang'       => now(),
            ]);
        }

        $pesan = match ($validated['status']) {
            'disetujui' => 'Izin penjemputan berhasil disetujui.',
            'ditolak'   => 'Izin penjemputan telah ditolak.',
            'selesai'   => 'Penyerahan anak selesai dikonfirmasi.',
        };

        return redirect()->route('pengasuh.penjemputan.index')
            ->with('success', $pesan);
    }
}
