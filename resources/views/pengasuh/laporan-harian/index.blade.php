@extends('layouts.pengasuh')

@section('title', 'Laporan Harian Anak')

@section('content')
    {{-- Header Halaman --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#A8D4FF] border border-[#1B1B1B]">
                    Pengasuh
                </span>
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#FF9E80] border border-[#1B1B1B]">
                    05 Operasional Harian
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Laporan Harian Anak</h1>
            <p class="text-sm text-[#6B6B6B] mt-1">
                Catat perkembangan makan, tidur, BAB/BAK, aktivitas, serta unggah dokumentasi kegiatan anak untuk orang tua.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('pengasuh.laporan-harian.create') }}"
               class="inline-flex items-center justify-center gap-2 font-semibold text-sm px-5 py-3 rounded-[10px] bg-[#FFD43B] text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline">
                <span>+ Buat Laporan Harian</span>
            </a>
        </div>
    </header>

    {{-- Kartu Statistik --}}
    <section aria-label="Ringkasan Laporan Harian" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#A8D4FF] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['hari_ini'] }}</div>
            <div class="text-sm font-bold mt-2">Laporan Hari Ini</div>
        </div>
        <div class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['total_semua'] }}</div>
            <div class="text-sm font-bold mt-2">Total Laporan</div>
        </div>
        <div class="bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['perlu_pantau'] }}</div>
            <div class="text-sm font-bold mt-2">Perlu Pemantauan</div>
        </div>
        <div class="bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['total_foto'] }}</div>
            <div class="text-sm font-bold mt-2">Foto Kegiatan</div>
        </div>
    </section>

    {{-- Toolbar Pencarian & Filter --}}
    <form method="GET" action="{{ route('pengasuh.laporan-harian.index') }}"
          class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="relative md:col-span-5">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm pointer-events-none">🔍</span>
                <input type="search"
                       name="q"
                       value="{{ $searchAktif }}"
                       placeholder="Cari nama anak, kode, atau aktivitas..."
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
                <input type="date"
                       name="tanggal"
                       value="{{ $tanggalAktif }}"
                       aria-label="Filter tanggal"
                       class="w-full px-3.5 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none">
            </div>

            @if ($kondisiAktif)
                <input type="hidden" name="kondisi" value="{{ $kondisiAktif }}">
            @endif

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Filter
                </button>
                @if ($searchAktif !== '' || $anakIdAktif || $tanggalAktif || $kondisiAktif)
                    <a href="{{ route('pengasuh.laporan-harian.index') }}"
                       class="inline-flex items-center justify-center font-semibold text-sm px-3 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] no-underline text-[#1B1B1B]">
                        Reset
                    </a>
                @endif
            </div>
        </div>

        {{-- Chip Filter Kondisi Anak --}}
        <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter kondisi anak">
            <a href="{{ route('pengasuh.laporan-harian.index', array_filter(['q' => $searchAktif, 'anak_id' => $anakIdAktif, 'tanggal' => $tanggalAktif])) }}"
               class="text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#1B1B1B] no-underline text-[#1B1B1B] {{ empty($kondisiAktif) ? 'bg-[#FFD43B] border-[1.5px]' : 'bg-white hover:bg-[#FFF6D6]' }}">
                Semua Kondisi
            </a>
            @foreach ($daftarKondisi as $kodeKondisi => $labelKondisi)
                <a href="{{ route('pengasuh.laporan-harian.index', array_filter(['kondisi' => $kodeKondisi, 'q' => $searchAktif, 'anak_id' => $anakIdAktif, 'tanggal' => $tanggalAktif])) }}"
                   class="text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#1B1B1B] no-underline text-[#1B1B1B] {{ $kondisiAktif === $kodeKondisi ? 'bg-[#FFD43B] border-[1.5px]' : 'bg-white hover:bg-[#FFF6D6]' }}">
                    {{ $labelKondisi }}
                </a>
            @endforeach
        </div>
    </form>

    {{-- Tabel Daftar Laporan Harian (Stage 7 & Stage 2) --}}
    <div class="border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-white">
        @if ($daftarLaporan->isEmpty())
            <div class="py-12 px-4 text-center">
                <span class="inline-grid place-items-center w-14 h-14 rounded-full bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] text-2xl mb-3">
                    📋
                </span>
                <h2 class="text-lg font-bold">Belum ada laporan harian ditemukan</h2>
                <p class="text-sm text-[#6B6B6B] max-w-md mx-auto mt-1">
                    Mulai catat aktivitas makan, tidur, BAB/BAK, dan kondisi anak hari ini agar orang tua dapat memantau perkembangan buah hatinya.
                </p>
                <a href="{{ route('pengasuh.laporan-harian.create') }}"
                   class="inline-flex items-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] mt-4 no-underline text-[#1B1B1B]">
                    + Buat Laporan Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-[#FFD43B] border-b-[1.5px] border-[#1B1B1B] text-left font-bold">
                            <th class="py-3 px-4">Tanggal &amp; Anak</th>
                            <th class="py-3 px-4">Makan &amp; Tidur</th>
                            <th class="py-3 px-4">BAB/BAK &amp; Aktivitas</th>
                            <th class="py-3 px-4">Kondisi</th>
                            <th class="py-3 px-4">Foto</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1B1B1B]">
                        @foreach ($daftarLaporan as $laporan)
                            @php
                                $kondisiClass = match ($laporan->kondisi_tone) {
                                    'mint'     => 'bg-[#A8E6CF] text-[#1B1B1B]',
                                    'yellow'   => 'bg-[#FFD43B] text-[#1B1B1B]',
                                    'coral'    => 'bg-[#FF9E80] text-[#1B1B1B]',
                                    'lavender' => 'bg-[#CDBDFF] text-[#1B1B1B]',
                                    default    => 'bg-white text-[#1B1B1B]',
                                };
                                $fotoListJson = $laporan->foto->map(fn ($f) => [
                                    'url'        => $f->foto_url,
                                    'keterangan' => $f->keterangan ?? 'Dokumentasi kegiatan harian',
                                ])->values();
                            @endphp
                            <tr class="even:bg-[#FFF6D6]">
                                <td class="py-3.5 px-4 align-top">
                                    <span class="inline-block text-xs font-bold px-2.5 py-0.5 rounded-full bg-white border border-[#1B1B1B] mb-1">
                                        {{ $laporan->tanggal?->translatedFormat('d M Y') }}
                                    </span>
                                    <div class="font-bold">{{ $laporan->anak?->nama_lengkap ?? '-' }}</div>
                                    <div class="text-xs text-[#6B6B6B]">
                                        {{ $laporan->anak?->kode_anak }} · {{ $laporan->anak?->kelas?->nama_kelas ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-[#6B6B6B] mt-0.5">
                                        Dicatat: {{ $laporan->pengasuh?->name ?? '-' }}
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 align-top max-w-[230px]">
                                    <div class="text-xs">
                                        <span class="font-bold">Makan:</span>
                                        <span class="line-clamp-2">{{ $laporan->makan }}</span>
                                    </div>
                                    <div class="text-xs mt-1">
                                        <span class="font-bold">Tidur:</span>
                                        <span>{{ $laporan->tidur }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 align-top max-w-[240px]">
                                    <div class="text-xs">
                                        <span class="font-bold">BAB/BAK:</span>
                                        <span>{{ $laporan->bab_bak }}</span>
                                    </div>
                                    <div class="text-xs mt-1">
                                        <span class="font-bold">Aktivitas:</span>
                                        <span class="line-clamp-2">{{ $laporan->aktivitas }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                    <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full border border-[#1B1B1B] {{ $kondisiClass }}">
                                        {{ $laporan->kondisi_label }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                    @if ($laporan->foto->isNotEmpty())
                                        <div class="flex items-center gap-1.5">
                                            <img src="{{ $laporan->foto->first()->foto_url }}"
                                                 alt="Thumbnail kegiatan"
                                                 class="w-10 h-10 rounded-[8px] object-cover border border-[#1B1B1B]">
                                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-[#A8D4FF] border border-[#1B1B1B]">
                                                {{ $laporan->foto->count() }} foto
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-[#6B6B6B]">Belum ada foto</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 align-top text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button"
                                                data-detail-laporan
                                                data-anak="{{ $laporan->anak?->nama_lengkap }}"
                                                data-tanggal="{{ $laporan->tanggal?->translatedFormat('l, d M Y') }}"
                                                data-pengasuh="{{ $laporan->pengasuh?->name }}"
                                                data-kondisi="{{ $laporan->kondisi_label }}"
                                                data-makan="{{ $laporan->makan }}"
                                                data-tidur="{{ $laporan->tidur }}"
                                                data-babbak="{{ $laporan->bab_bak }}"
                                                data-aktivitas="{{ $laporan->aktivitas }}"
                                                data-catatan="{{ $laporan->catatan_tambahan ?? 'Tidak ada catatan tambahan.' }}"
                                                data-fotos='@json($fotoListJson)'
                                                class="inline-flex items-center font-semibold text-xs px-3 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                            Lihat
                                        </button>

                                        <a href="{{ route('pengasuh.laporan-harian.edit', $laporan->id) }}"
                                           class="inline-flex items-center font-semibold text-xs px-3 py-2 rounded-[10px] bg-[#A8D4FF] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
                                            Ubah &amp; Foto
                                        </a>

                                        <button type="button"
                                                data-delete-laporan
                                                data-action="{{ route('pengasuh.laporan-harian.destroy', $laporan->id) }}"
                                                data-anak="{{ $laporan->anak?->nama_lengkap }}"
                                                data-tanggal="{{ $laporan->tanggal?->translatedFormat('d M Y') }}"
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

            @if ($daftarLaporan->hasPages())
                <div class="p-4 border-t-[1.5px] border-[#1B1B1B] bg-[#FFF6D6]">
                    {{ $daftarLaporan->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Modal Lihat Rincian Laporan Harian & Galeri Foto (<dialog>) --}}
    <dialog id="modal-detail-laporan" class="ta-modal p-0 border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(680px,calc(100vw-32px))] max-h-[calc(100vh-48px)] overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#A8D4FF] border-b-[1.5px] border-[#1B1B1B]">
            <h3 class="text-lg font-bold m-0">Rincian Laporan Harian Anak</h3>
            <button type="button" data-close-modal="modal-detail-laporan" aria-label="Tutup"
                    class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                ✕
            </button>
        </div>

        <div class="p-5 overflow-y-auto space-y-4 text-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4">
                <div>
                    <div class="text-xs text-[#6B6B6B]">Nama Anak</div>
                    <div class="font-bold" id="dl-anak">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Tanggal Laporan</div>
                    <div class="font-bold" id="dl-tanggal">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Kondisi Umum</div>
                    <div class="font-bold" id="dl-kondisi">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Pengasuh Pencatat</div>
                    <div class="font-semibold" id="dl-pengasuh">-</div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3.5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white">
                    <div class="text-xs font-bold mb-1">🍽️ Konsumsi Makan &amp; Minum</div>
                    <div class="text-xs whitespace-pre-line" id="dl-makan">-</div>
                </div>
                <div class="p-3.5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white">
                    <div class="text-xs font-bold mb-1">😴 Tidur / Istirahat</div>
                    <div class="text-xs whitespace-pre-line" id="dl-tidur">-</div>
                </div>
                <div class="p-3.5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white">
                    <div class="text-xs font-bold mb-1">🚽 Catatan BAB &amp; BAK</div>
                    <div class="text-xs whitespace-pre-line" id="dl-babbak">-</div>
                </div>
                <div class="p-3.5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white">
                    <div class="text-xs font-bold mb-1">🎨 Aktivitas &amp; Bermain</div>
                    <div class="text-xs whitespace-pre-line" id="dl-aktivitas">-</div>
                </div>
            </div>

            <div class="p-3.5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-[#FFF6D6]">
                <div class="text-xs font-bold mb-1">📝 Catatan Tambahan untuk Orang Tua</div>
                <div class="text-xs whitespace-pre-line" id="dl-catatan">-</div>
            </div>

            <div id="dl-galeri-wrap" class="space-y-2 hidden">
                <div class="text-xs font-bold">📸 Dokumentasi Foto Kegiatan</div>
                <div id="dl-galeri-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
            </div>
        </div>

        <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
            <button type="button" data-close-modal="modal-detail-laporan"
                    class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#1B1B1B] text-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                Tutup
            </button>
        </div>
    </dialog>

    {{-- Modal Konfirmasi Hapus Laporan Harian (Stage 2 — Header Coral) --}}
    <dialog id="modal-hapus-laporan" class="ta-modal p-0 border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(480px,calc(100vw-32px))] overflow-hidden">
        <form id="form-hapus-laporan" method="POST" action="">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FF9E80] border-b-[1.5px] border-[#1B1B1B]">
                <h3 class="text-lg font-bold m-0">Hapus Laporan Harian?</h3>
                <button type="button" data-close-modal="modal-hapus-laporan" aria-label="Tutup"
                        class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="p-5 space-y-2.5 text-sm">
                <p class="m-0">
                    Anda yakin ingin menghapus laporan harian <b id="hl-anak">Anak</b> tanggal <b id="hl-tanggal">-</b>?
                </p>
                <p class="text-xs text-[#6B6B6B] m-0">
                    Seluruh data catatan harian beserta foto kegiatan yang terlampir pada laporan ini akan dihapus permanen.
                </p>
            </div>

            <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                <button type="button" data-close-modal="modal-hapus-laporan"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Ya, Hapus Permanen
                </button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('modal-detail-laporan');
    const hapusModal  = document.getElementById('modal-hapus-laporan');

    // Detail Laporan Harian
    document.querySelectorAll('[data-detail-laporan]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('dl-anak').textContent      = btn.dataset.anak || '-';
            document.getElementById('dl-tanggal').textContent   = btn.dataset.tanggal || '-';
            document.getElementById('dl-kondisi').textContent   = btn.dataset.kondisi || '-';
            document.getElementById('dl-pengasuh').textContent  = btn.dataset.pengasuh || '-';
            document.getElementById('dl-makan').textContent     = btn.dataset.makan || '-';
            document.getElementById('dl-tidur').textContent     = btn.dataset.tidur || '-';
            document.getElementById('dl-babbak').textContent    = btn.dataset.babbak || '-';
            document.getElementById('dl-aktivitas').textContent = btn.dataset.aktivitas || '-';
            document.getElementById('dl-catatan').textContent   = btn.dataset.catatan || '-';

            const galeriWrap = document.getElementById('dl-galeri-wrap');
            const galeriGrid = document.getElementById('dl-galeri-grid');
            const fotos      = JSON.parse(btn.dataset.fotos || '[]');

            galeriGrid.innerHTML = '';
            if (fotos.length > 0) {
                fotos.forEach(function (item) {
                    const card = document.createElement('div');
                    card.className = 'border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6]';

                    const img = document.createElement('img');
                    img.src = item.url;
                    img.alt = item.keterangan || 'Foto kegiatan';
                    img.className = 'w-full h-32 object-cover border-b border-[#1B1B1B]';

                    const cap = document.createElement('div');
                    cap.className = 'p-2 text-[11px] font-medium truncate';
                    cap.textContent = item.keterangan || 'Dokumentasi kegiatan';

                    card.appendChild(img);
                    card.appendChild(cap);
                    galeriGrid.appendChild(card);
                });
                galeriWrap.classList.remove('hidden');
            } else {
                galeriWrap.classList.add('hidden');
            }

            if (detailModal && detailModal.showModal) detailModal.showModal();
        });
    });

    // Konfirmasi Hapus Laporan
    document.querySelectorAll('[data-delete-laporan]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('form-hapus-laporan').action = btn.dataset.action;
            document.getElementById('hl-anak').textContent       = btn.dataset.anak || 'Anak';
            document.getElementById('hl-tanggal').textContent    = btn.dataset.tanggal || '-';

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