@extends('layouts.pengasuh')

@section('title', 'Laporan Insiden Anak')

@section('content')
    {{-- Header Halaman --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#A8D4FF] border border-[#1B1B1B]">
                    Pengasuh
                </span>
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#FF9E80] border border-[#1B1B1B]">
                    05 Operasional · Dokumentasi Insiden
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Laporan Insiden Anak</h1>
            <p class="text-sm text-[#6B6B6B] mt-1">
                Catat setiap kejadian insiden (terjatuh, tergores, demam mendadak) beserta tindakan pertolongan pertama agar terdokumentasi dengan jelas.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('pengasuh.laporan-insiden.create') }}"
               class="inline-flex items-center justify-center gap-2 font-semibold text-sm px-5 py-3 rounded-[10px] bg-[#FFD43B] text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline">
                <span>+ Catat Laporan Insiden</span>
            </a>
        </div>
    </header>

    {{-- Kartu Statistik --}}
    <section aria-label="Ringkasan Laporan Insiden" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['total'] }}</div>
            <div class="text-sm font-bold mt-2">Total Insiden</div>
        </div>
        <div class="bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['ditangani'] }}</div>
            <div class="text-sm font-bold mt-2">Sedang Ditangani</div>
        </div>
        <div class="bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['pemantauan'] }}</div>
            <div class="text-sm font-bold mt-2">Dalam Pemantauan</div>
        </div>
        <div class="bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['selesai'] }}</div>
            <div class="text-sm font-bold mt-2">Selesai Ditangani</div>
        </div>
    </section>

    {{-- Toolbar Pencarian & Filter --}}
    <form method="GET" action="{{ route('pengasuh.laporan-insiden.index') }}"
          class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="relative md:col-span-5">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm pointer-events-none">🔍</span>
                <input type="search"
                       name="q"
                       value="{{ $searchAktif }}"
                       placeholder="Cari judul insiden, kronologi, atau nama anak..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none">
            </div>

            <div class="md:col-span-3">
                <select name="anak_id"
                        aria-label="Filter anak"
                        class="w-full px-3.5 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none">
                    <option value="">Semua Anak</option>
                    @foreach ($daftarAnak as $anak)
                        <option value="{{ $anak->id }}" @selected((string) $anakIdAktif === (string) $anak->id)>
                            {{ $anak->nama_tampil }} ({{ $anak->kode_anak }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="keparahan"
                        aria-label="Filter tingkat keparahan"
                        class="w-full px-3.5 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none">
                    <option value="">Semua Keparahan</option>
                    @foreach ($daftarKeparahan as $kodeKeparahan => $labelKeparahan)
                        <option value="{{ $kodeKeparahan }}" @selected($keparahanAktif === $kodeKeparahan)>
                            {{ $labelKeparahan }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if ($statusAktif)
                <input type="hidden" name="status" value="{{ $statusAktif }}">
            @endif

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Filter
                </button>
                @if ($searchAktif !== '' || $anakIdAktif || $keparahanAktif || $statusAktif)
                    <a href="{{ route('pengasuh.laporan-insiden.index') }}"
                       class="inline-flex items-center justify-center font-semibold text-sm px-3 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] no-underline text-[#1B1B1B]">
                        Reset
                    </a>
                @endif
            </div>
        </div>

        {{-- Chip Filter Status Penanganan --}}
        <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter status penanganan">
            <a href="{{ route('pengasuh.laporan-insiden.index', array_filter(['q' => $searchAktif, 'anak_id' => $anakIdAktif, 'keparahan' => $keparahanAktif])) }}"
               class="text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#1B1B1B] no-underline text-[#1B1B1B] {{ empty($statusAktif) ? 'bg-[#FFD43B] border-[1.5px]' : 'bg-white hover:bg-[#FFF6D6]' }}">
                Semua Status
            </a>
            @foreach ($daftarStatus as $kodeStatus => $labelStatus)
                <a href="{{ route('pengasuh.laporan-insiden.index', array_filter(['status' => $kodeStatus, 'q' => $searchAktif, 'anak_id' => $anakIdAktif, 'keparahan' => $keparahanAktif])) }}"
                   class="text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#1B1B1B] no-underline text-[#1B1B1B] {{ $statusAktif === $kodeStatus ? 'bg-[#FFD43B] border-[1.5px]' : 'bg-white hover:bg-[#FFF6D6]' }}">
                    {{ $labelStatus }}
                </a>
            @endforeach
        </div>
    </form>

    {{-- Tabel Daftar Laporan Insiden --}}
    <div class="border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-white">
        @if ($daftarInsiden->isEmpty())
            <div class="py-12 px-4 text-center">
                <span class="inline-grid place-items-center w-14 h-14 rounded-full bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] text-2xl mb-3">
                    🩹
                </span>
                <h2 class="text-lg font-bold">Belum ada laporan insiden tercatat</h2>
                <p class="text-sm text-[#6B6B6B] max-w-md mx-auto mt-1">
                    Tidak ditemukan laporan insiden sesuai filter Anda. Apabila terjadi insiden pada anak, segera dokumentasikan di sini.
                </p>
                <a href="{{ route('pengasuh.laporan-insiden.create') }}"
                   class="inline-flex items-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] mt-4 no-underline text-[#1B1B1B]">
                    + Catat Laporan Insiden
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-[#FFD43B] border-b-[1.5px] border-[#1B1B1B] text-left font-bold">
                            <th class="py-3 px-4">Waktu &amp; Anak</th>
                            <th class="py-3 px-4">Insiden &amp; Kronologi</th>
                            <th class="py-3 px-4">Keparahan</th>
                            <th class="py-3 px-4">Status Penanganan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1B1B1B]">
                        @foreach ($daftarInsiden as $insiden)
                            @php
                                $keparahanClass = match ($insiden->keparahan_tone) {
                                    'blue'   => 'bg-[#A8D4FF] text-[#1B1B1B]',
                                    'yellow' => 'bg-[#FFD43B] text-[#1B1B1B]',
                                    'coral'  => 'bg-[#FF9E80] text-[#1B1B1B]',
                                    default  => 'bg-white text-[#1B1B1B]',
                                };
                                $statusClass = match ($insiden->status_tone) {
                                    'coral'  => 'bg-[#FF9E80] text-[#1B1B1B]',
                                    'yellow' => 'bg-[#FFD43B] text-[#1B1B1B]',
                                    'mint'   => 'bg-[#A8E6CF] text-[#1B1B1B]',
                                    default  => 'bg-white text-[#1B1B1B]',
                                };
                            @endphp
                            <tr class="even:bg-[#FFF6D6]">
                                <td class="py-3.5 px-4 align-top">
                                    <span class="inline-block text-xs font-bold px-2.5 py-0.5 rounded-full bg-white border border-[#1B1B1B] mb-1">
                                        {{ $insiden->waktu_kejadian?->translatedFormat('d M Y · H:i') }} WIB
                                    </span>
                                    <div class="font-bold">{{ $insiden->anak?->nama_lengkap ?? '-' }}</div>
                                    <div class="text-xs text-[#6B6B6B]">
                                        {{ $insiden->anak?->kode_anak }} · {{ $insiden->anak?->kelas?->nama_kelas ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-[#6B6B6B] mt-0.5">
                                        Pelapor: {{ $insiden->pengasuh?->name ?? '-' }}
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 align-top max-w-[300px]">
                                    <div class="font-bold text-sm">{{ $insiden->judul_insiden }}</div>
                                    <p class="text-xs text-[#6B6B6B] mt-1 line-clamp-2">
                                        {{ $insiden->kronologi }}
                                    </p>
                                    @if ($insiden->foto_bukti)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-white border border-[#1B1B1B] mt-1.5">
                                            📎 Ada Foto Bukti
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                    <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full border border-[#1B1B1B] {{ $keparahanClass }}">
                                        {{ $insiden->keparahan_label }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                    <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full border border-[#1B1B1B] {{ $statusClass }}">
                                        {{ $insiden->status_label }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 align-top text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button"
                                                data-detail-insiden
                                                data-anak="{{ $insiden->anak?->nama_lengkap }}"
                                                data-waktu="{{ $insiden->waktu_kejadian?->translatedFormat('l, d M Y · H:i') }} WIB"
                                                data-judul="{{ $insiden->judul_insiden }}"
                                                data-keparahan="{{ $insiden->keparahan_label }}"
                                                data-status="{{ $insiden->status_label }}"
                                                data-pengasuh="{{ $insiden->pengasuh?->name }}"
                                                data-kronologi="{{ $insiden->kronologi }}"
                                                data-tindakan="{{ $insiden->tindakan_pertolongan }}"
                                                data-foto="{{ $insiden->foto_bukti_url ?? '' }}"
                                                class="inline-flex items-center font-semibold text-xs px-3 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                            Detail
                                        </button>

                                        <a href="{{ route('pengasuh.laporan-insiden.edit', $insiden->id) }}"
                                           class="inline-flex items-center font-semibold text-xs px-3 py-2 rounded-[10px] bg-[#A8D4FF] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
                                            Ubah Info
                                        </a>

                                        <button type="button"
                                                data-delete-insiden
                                                data-action="{{ route('pengasuh.laporan-insiden.destroy', $insiden->id) }}"
                                                data-judul="{{ $insiden->judul_insiden }}"
                                                data-anak="{{ $insiden->anak?->nama_lengkap }}"
                                                class="inline-flex items-center font-semibold text-xs px-3 py-2 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($daftarInsiden->hasPages())
                <div class="p-4 border-t-[1.5px] border-[#1B1B1B] bg-[#FFF6D6]">
                    {{ $daftarInsiden->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Modal Detail Laporan Insiden (<dialog>) --}}
    <dialog id="modal-detail-insiden" class="ta-modal p-0 border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(640px,calc(100vw-32px))] max-h-[calc(100vh-48px)] overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FF9E80] border-b-[1.5px] border-[#1B1B1B]">
            <h3 class="text-lg font-bold m-0">Detail Dokumentasi Insiden</h3>
            <button type="button" data-close-modal="modal-detail-insiden" aria-label="Tutup"
                    class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                ✕
            </button>
        </div>

        <div class="p-5 overflow-y-auto space-y-4 text-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4">
                <div>
                    <div class="text-xs text-[#6B6B6B]">Nama Anak</div>
                    <div class="font-bold" id="di-anak">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Waktu Kejadian</div>
                    <div class="font-bold" id="di-waktu">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Tingkat Keparahan</div>
                    <div class="font-bold" id="di-keparahan">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Status Penanganan</div>
                    <div class="font-bold" id="di-status">-</div>
                </div>
                <div class="sm:col-span-2">
                    <div class="text-xs text-[#6B6B6B]">Judul Insiden</div>
                    <div class="font-bold text-base" id="di-judul">-</div>
                </div>
            </div>

            <div class="p-3.5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white">
                <div class="text-xs font-bold mb-1">📋 Kronologi Kejadian</div>
                <div class="text-xs whitespace-pre-line" id="di-kronologi">-</div>
            </div>

            <div class="p-3.5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-[#A8E6CF]">
                <div class="text-xs font-bold mb-1">🩹 Tindakan Pertolongan Pertama</div>
                <div class="text-xs whitespace-pre-line" id="di-tindakan">-</div>
            </div>

            <div id="di-foto-wrap" class="hidden space-y-1.5">
                <div class="text-xs font-bold">📸 Foto Dokumentasi Insiden</div>
                <div class="border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6] p-2">
                    <img id="di-foto-img" src="" alt="Bukti dokumentasi insiden" class="w-full max-h-64 object-contain mx-auto rounded-[8px]">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
            <button type="button" data-close-modal="modal-detail-insiden"
                    class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#1B1B1B] text-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                Tutup
            </button>
        </div>
    </dialog>

    {{-- Modal Konfirmasi Hapus Laporan Insiden (<dialog> Header Coral) --}}
    <dialog id="modal-hapus-insiden" class="ta-modal p-0 border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(480px,calc(100vw-32px))] overflow-hidden">
        <form id="form-hapus-insiden" method="POST" action="">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FF9E80] border-b-[1.5px] border-[#1B1B1B]">
                <h3 class="text-lg font-bold m-0">Hapus Laporan Insiden?</h3>
                <button type="button" data-close-modal="modal-hapus-insiden" aria-label="Tutup"
                        class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="p-5 space-y-2.5 text-sm">
                <p class="m-0">
                    Anda yakin ingin menghapus laporan insiden <b id="hi-judul">Insiden</b> untuk <b id="hi-anak">Anak</b>?
                </p>
                <p class="text-xs text-[#6B6B6B] m-0">
                    Data catatan insiden dan foto bukti yang terlampir akan dihapus permanen.
                </p>
            </div>

            <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                <button type="button" data-close-modal="modal-hapus-insiden"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Ya, Hapus Insiden
                </button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('modal-detail-insiden');
    const hapusModal  = document.getElementById('modal-hapus-insiden');

    document.querySelectorAll('[data-detail-insiden]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('di-anak').textContent      = btn.dataset.anak || '-';
            document.getElementById('di-waktu').textContent     = btn.dataset.waktu || '-';
            document.getElementById('di-judul').textContent     = btn.dataset.judul || '-';
            document.getElementById('di-keparahan').textContent = btn.dataset.keparahan || '-';
            document.getElementById('di-status').textContent    = btn.dataset.status || '-';
            document.getElementById('di-kronologi').textContent = btn.dataset.kronologi || '-';
            document.getElementById('di-tindakan').textContent  = btn.dataset.tindakan || '-';

            const fotoWrap = document.getElementById('di-foto-wrap');
            const fotoImg  = document.getElementById('di-foto-img');
            if (btn.dataset.foto) {
                fotoImg.src = btn.dataset.foto;
                fotoWrap.classList.remove('hidden');
            } else {
                fotoImg.src = '';
                fotoWrap.classList.add('hidden');
            }

            if (detailModal && detailModal.showModal) detailModal.showModal();
        });
    });

    document.querySelectorAll('[data-delete-insiden]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('form-hapus-insiden').action = btn.dataset.action;
            document.getElementById('hi-judul').textContent      = btn.dataset.judul || 'Insiden';
            document.getElementById('hi-anak').textContent       = btn.dataset.anak || 'Anak';

            if (hapusModal && hapusModal.showModal) hapusModal.showModal();
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(btn.dataset.closeModal);
            if (target) target.close();
        });
    });

    [detailModal, hapusModal].forEach(function (dialog) {
        if (!dialog) return;
        dialog.addEventListener('click', function (e) {
            if (e.target === dialog) dialog.close();
        });
    });
});
</script>
@endpush