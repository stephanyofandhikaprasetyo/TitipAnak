@extends('layouts.orangtua')

@section('title', 'Penjemputan Anak')

@section('content')
    {{-- Header Halaman --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-[#A8E6CF] border border-[#1B1B1B]">
                    Orang Tua
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-[#FF9E80] border border-[#1B1B1B]">
                    Operasional Penjemputan
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Penjemputan Anak</h1>
            <p class="text-sm text-[#6B6B6B] mt-1">
                Ajukan jadwal penjemputan, pantau status verifikasi pengasuh, dan kelola wali penjemput terotorisasi.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('orangtua.penjemputan.create') }}"
               class="inline-flex items-center justify-center gap-2 font-semibold text-sm px-5 py-3 rounded-[10px] bg-[#FFD43B] text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline">
                <span>+ Ajukan Penjemputan</span>
            </a>
        </div>
    </header>

    {{-- Banner Informasi Keamanan --}}
    <div class="flex items-center gap-3.5 bg-[#1B1B1B] text-white border-[1.5px] border-[#1B1B1B] rounded-[10px] px-5 py-4 text-sm font-medium">
        <span class="inline-grid place-items-center w-10 h-10 rounded-full bg-white text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] shrink-0 text-base">
            🛡️
        </span>
        <span>
            Demi keamanan anak, penjemput wajib membawa KTP asli sesuai data yang diajukan untuk diverifikasi pengasuh saat tiba di daycare.
        </span>
    </div>

    {{-- Kartu Statistik --}}
    <section aria-label="Ringkasan Penjemputan" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['total'] }}</div>
            <div class="text-sm font-bold mt-2">Total Pengajuan</div>
        </div>
        <div class="bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['menunggu'] }}</div>
            <div class="text-sm font-bold mt-2">Menunggu Verifikasi</div>
        </div>
        <div class="bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['disetujui'] }}</div>
            <div class="text-sm font-bold mt-2">Disetujui</div>
        </div>
        <div class="bg-[#A8D4FF] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['selesai'] }}</div>
            <div class="text-sm font-bold mt-2">Selesai Dijemput</div>
        </div>
    </section>

    {{-- Status Anak Terdaftar --}}
    <section aria-label="Status Kehadiran Anak" class="space-y-3">
        <h2 class="text-lg font-bold">Anak Terdaftar Anda</h2>

        @if ($daftarAnak->isEmpty())
            <div class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-6 text-center">
                <p class="font-bold text-base">Belum ada data anak aktif pada akun Anda.</p>
                <p class="text-sm text-[#6B6B6B] mt-1">Hubungi admin daycare jika data anak belum terhubung ke akun ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($daftarAnak as $anak)
                    @php
                        $toneBadge = match ($anak->status_kehadiran_tone) {
                            'blue'   => 'bg-[#A8D4FF] text-[#1B1B1B]',
                            'yellow' => 'bg-[#FFD43B] text-[#1B1B1B]',
                            'mint'   => 'bg-[#A8E6CF] text-[#1B1B1B]',
                            default  => 'bg-[#1B1B1B] text-white',
                        };
                    @endphp
                    <div class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 flex flex-col justify-between gap-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <span class="inline-grid place-items-center w-12 h-12 rounded-full bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] font-bold text-lg shrink-0">
                                    {{ $anak->inisial }}
                                </span>
                                <div class="min-w-0">
                                    <div class="font-bold text-base truncate">{{ $anak->nama_lengkap }}</div>
                                    <div class="text-xs text-[#6B6B6B]">
                                        {{ $anak->kode_anak }} · {{ $anak->kelas?->nama_kelas ?? 'Tanpa Kelas' }}
                                    </div>
                                </div>
                            </div>

                            <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full border border-[#1B1B1B] shrink-0 {{ $toneBadge }}">
                                {{ $anak->status_kehadiran_label }}
                            </span>
                        </div>

                        @if ($anak->catatan_medis)
                            <div class="text-xs bg-[#FFF6D6] border border-[#1B1B1B] rounded-[10px] px-3 py-2">
                                <span class="font-bold">Catatan Medis:</span> {{ $anak->catatan_medis }}
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-[#1B1B1B]">
                            <span class="text-xs text-[#6B6B6B]">
                                Penjemput terdaftar: <b class="text-[#1B1B1B]">{{ $anak->penjemput->count() }} orang</b>
                            </span>

                            @if ($anak->bolehAjukanPenjemputan())
                                <a href="{{ route('orangtua.penjemputan.create', ['anak_id' => $anak->id]) }}"
                                   class="inline-flex items-center font-semibold text-xs px-3.5 py-2 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
                                    Ajukan Jemput
                                </a>
                            @else
                                <span class="text-xs font-semibold text-[#6B6B6B]">Tidak dapat diajukan</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Filter & Tabel Riwayat Penjemputan --}}
    <section aria-label="Riwayat Pengajuan Penjemputan" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="text-lg font-bold">Riwayat Pengajuan Penjemputan</h2>
            <span class="text-xs text-[#6B6B6B]">Menampilkan {{ $riwayatPenjemputan->total() }} data pengajuan</span>
        </div>

        {{-- Toolbar Pencarian & Filter --}}
        <form method="GET" action="{{ route('orangtua.penjemputan.index') }}" class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 space-y-3">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm pointer-events-none">🔍</span>
                    <input type="search"
                           name="q"
                           value="{{ $searchAktif }}"
                           placeholder="Cari nama anak atau nama penjemput..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6]">
                </div>

                <select name="anak_id"
                        aria-label="Filter berdasarkan anak"
                        class="px-3.5 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none">
                    <option value="">Semua Anak</option>
                    @foreach ($daftarAnak as $anak)
                        <option value="{{ $anak->id }}" @selected((string) $anakIdAktif === (string) $anak->id)>
                            {{ $anak->nama_tampil }} ({{ $anak->kode_anak }})
                        </option>
                    @endforeach
                </select>

                @if ($statusAktif)
                    <input type="hidden" name="status" value="{{ $statusAktif }}">
                @endif

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                        Terapkan
                    </button>

                    @if ($searchAktif !== '' || $anakIdAktif || $statusAktif)
                        <a href="{{ route('orangtua.penjemputan.index') }}"
                           class="inline-flex items-center justify-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] no-underline text-[#1B1B1B]">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            {{-- Chip Filter Cepat Status --}}
            <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter status pengajuan">
                <a href="{{ route('orangtua.penjemputan.index', array_filter(['q' => $searchAktif, 'anak_id' => $anakIdAktif])) }}"
                   class="text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#1B1B1B] no-underline text-[#1B1B1B] {{ empty($statusAktif) ? 'bg-[#FFD43B] border-[1.5px]' : 'bg-white hover:bg-[#FFF6D6]' }}">
                    Semua
                </a>
                @foreach ($daftarStatus as $kodeStatus => $labelStatus)
                    <a href="{{ route('orangtua.penjemputan.index', array_filter(['status' => $kodeStatus, 'q' => $searchAktif, 'anak_id' => $anakIdAktif])) }}"
                       class="text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#1B1B1B] no-underline text-[#1B1B1B] {{ $statusAktif === $kodeStatus ? 'bg-[#FFD43B] border-[1.5px]' : 'bg-white hover:bg-[#FFF6D6]' }}">
                        {{ $labelStatus }}
                    </a>
                @endforeach
            </div>
        </form>

        {{-- Tabel Data --}}
        <div class="border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-white">
            @if ($riwayatPenjemputan->isEmpty())
                <div class="py-12 px-4 text-center">
                    <span class="inline-grid place-items-center w-14 h-14 rounded-full bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] text-2xl mb-3">
                        🔎
                    </span>
                    <h3 class="text-lg font-bold">Belum ada riwayat penjemputan</h3>
                    <p class="text-sm text-[#6B6B6B] max-w-md mx-auto mt-1">
                        Tidak ditemukan data penjemputan yang sesuai filter Anda, atau Anda belum pernah membuat pengajuan penjemputan.
                    </p>
                    <a href="{{ route('orangtua.penjemputan.create') }}"
                       class="inline-flex items-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] mt-4 no-underline text-[#1B1B1B]">
                        + Buat Pengajuan Pertama
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-[#FFD43B] border-b-[1.5px] border-[#1B1B1B] text-left font-bold">
                                <th class="py-3 px-4">Anak</th>
                                <th class="py-3 px-4">Penjemput</th>
                                <th class="py-3 px-4">Jadwal Jemput</th>
                                <th class="py-3 px-4">Verifikasi</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1B1B1B]">
                            @foreach ($riwayatPenjemputan as $item)
                                @php
                                    $statusBadgeClass = match ($item->status_tone) {
                                        'yellow' => 'bg-[#FFD43B] text-[#1B1B1B]',
                                        'mint'   => 'bg-[#A8E6CF] text-[#1B1B1B]',
                                        'coral'  => 'bg-[#FF9E80] text-[#1B1B1B]',
                                        'blue'   => 'bg-[#A8D4FF] text-[#1B1B1B]',
                                        default  => 'bg-[#1B1B1B] text-white',
                                    };
                                    $hasFoto = filled($item->foto_bukti) || filled($item->penjemput?->foto);
                                @endphp
                                <tr class="even:bg-[#FFF6D6]">
                                    <td class="py-3.5 px-4 align-top">
                                        <div class="font-bold">{{ $item->anak?->nama_lengkap ?? '-' }}</div>
                                        <div class="text-xs text-[#6B6B6B]">
                                            {{ $item->anak?->kode_anak }} · {{ $item->anak?->kelas?->nama_kelas ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 align-top">
                                        <div class="font-bold">{{ $item->penjemput?->nama ?? '-' }}</div>
                                        <div class="text-xs text-[#6B6B6B] capitalize">
                                            {{ $item->penjemput?->hubungan ?? '-' }} · {{ $item->penjemput?->no_hp ?? '-' }}
                                        </div>
                                        @if ($item->penjemput?->nik_samar)
                                            <div class="text-xs text-[#6B6B6B]">NIK: {{ $item->penjemput->nik_samar }}</div>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                        <div class="font-semibold">{{ $item->waktu_jemput?->translatedFormat('d M Y') }}</div>
                                        <div class="text-xs text-[#6B6B6B]">Pukul {{ $item->waktu_jemput?->format('H:i') }} WIB</div>
                                    </td>

                                    <td class="py-3.5 px-4 align-top">
                                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-white border border-[#1B1B1B]">
                                            {{ $item->metode_verifikasi_label }}
                                        </span>
                                        @if ($item->catatan)
                                            <p class="text-xs text-[#6B6B6B] mt-1.5 max-w-[220px] line-clamp-2" title="{{ $item->catatan }}">
                                                "{{ $item->catatan }}"
                                            </p>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 align-top">
                                        <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full border border-[#1B1B1B] {{ $statusBadgeClass }}">
                                            {{ $item->status_label }}
                                        </span>
                                        <p class="text-xs text-[#6B6B6B] mt-1 max-w-[210px]">
                                            {{ $item->status_keterangan }}
                                        </p>
                                    </td>

                                    <td class="py-3.5 px-4 align-top text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <button type="button"
                                                    data-detail-btn
                                                    data-anak="{{ $item->anak?->nama_lengkap }}"
                                                    data-penjemput="{{ $item->penjemput?->nama }}"
                                                    data-hubungan="{{ ucfirst((string) $item->penjemput?->hubungan) }}"
                                                    data-nik="{{ $item->penjemput?->nik_samar ?? '-' }}"
                                                    data-hp="{{ $item->penjemput?->no_hp ?? '-' }}"
                                                    data-waktu="{{ $item->waktu_jemput?->translatedFormat('d M Y, H:i') }} WIB"
                                                    data-metode="{{ $item->metode_verifikasi_label }}"
                                                    data-status="{{ $item->status_label }}"
                                                    data-catatan="{{ $item->catatan ?? 'Tidak ada catatan tambahan.' }}"
                                                    data-foto="{{ $hasFoto ? route('orangtua.penjemputan.foto', $item->id) : '' }}"
                                                    class="inline-flex items-center font-semibold text-xs px-3 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                                Detail
                                            </button>

                                            @if ($item->bisaDibatalkan())
                                                <button type="button"
                                                        data-cancel-btn
                                                        data-action="{{ route('orangtua.penjemputan.cancel', $item->id) }}"
                                                        data-anak="{{ $item->anak?->nama_lengkap }}"
                                                        data-penjemput="{{ $item->penjemput?->nama }}"
                                                        class="inline-flex items-center font-semibold text-xs px-3 py-2 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                                    Batalkan
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($riwayatPenjemputan->hasPages())
                    <div class="p-4 border-t-[1.5px] border-[#1B1B1B] bg-[#FFF6D6]">
                        {{ $riwayatPenjemputan->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>

    {{-- Daftar Wali Penjemput Terotorisasi --}}
    <section aria-label="Daftar Wali Penjemput Terdaftar" class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold">Daftar Wali Penjemput Terdaftar</h2>
            <span class="text-xs text-[#6B6B6B]">{{ $daftarPenjemput->count() }} wali aktif</span>
        </div>

        @if ($daftarPenjemput->isEmpty())
            <div class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-sm text-[#6B6B6B]">
                Belum ada wali penjemput yang terdaftar. Wali penjemput otomatis tersimpan saat Anda mengajukan penjemputan baru.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($daftarPenjemput as $wali)
                    <div class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 flex items-start gap-3.5">
                        <span class="inline-grid place-items-center w-11 h-11 rounded-full bg-[#CDBDFF] border-[1.5px] border-[#1B1B1B] font-bold text-base shrink-0">
                            {{ $wali->inisial }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <div class="font-bold text-sm truncate">{{ $wali->nama }}</div>
                                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-[#A8E6CF] border border-[#1B1B1B] capitalize shrink-0">
                                    {{ $wali->hubungan }}
                                </span>
                            </div>
                            <div class="text-xs text-[#6B6B6B] mt-1">
                                Anak: <b class="text-[#1B1B1B]">{{ $wali->anak?->nama_tampil ?? '-' }}</b>
                            </div>
                            <div class="text-xs text-[#6B6B6B]">HP: {{ $wali->no_hp }}</div>
                            @if ($wali->nik_samar)
                                <div class="text-xs text-[#6B6B6B]">NIK: {{ $wali->nik_samar }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Modal Detail Penjemputan (<dialog>) --}}
    <dialog id="modal-detail" class="ta-modal p-0 border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(560px,calc(100vw-32px))] max-h-[calc(100vh-48px)] overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FFD43B] border-b-[1.5px] border-[#1B1B1B]">
            <h3 class="text-lg font-bold m-0">Detail Pengajuan Penjemputan</h3>
            <button type="button" data-close-modal="modal-detail" aria-label="Tutup"
                    class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                ✕
            </button>
        </div>

        <div class="p-5 overflow-y-auto space-y-4 text-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4">
                <div>
                    <div class="text-xs text-[#6B6B6B]">Nama Anak</div>
                    <div class="font-bold" id="md-anak">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Status Pengajuan</div>
                    <div class="font-bold" id="md-status">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Nama Penjemput</div>
                    <div class="font-bold" id="md-penjemput">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Hubungan &amp; No. HP</div>
                    <div class="font-semibold" id="md-hubungan-hp">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">NIK Terdaftar</div>
                    <div class="font-semibold" id="md-nik">-</div>
                </div>
                <div>
                    <div class="text-xs text-[#6B6B6B]">Jadwal Penjemputan</div>
                    <div class="font-semibold" id="md-waktu">-</div>
                </div>
                <div class="sm:col-span-2">
                    <div class="text-xs text-[#6B6B6B]">Metode Verifikasi</div>
                    <div class="font-semibold" id="md-metode">-</div>
                </div>
            </div>

            <div>
                <div class="text-xs font-bold mb-1">Catatan Orang Tua</div>
                <div class="p-3 rounded-[10px] border border-[#1B1B1B] bg-white text-xs" id="md-catatan">-</div>
            </div>

            <div id="md-foto-wrap" class="hidden space-y-2">
                <div class="text-xs font-bold">Foto Identitas / Bukti Penjemput</div>
                <div class="border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6]">
                    <img id="md-foto-img" src="" alt="Foto identitas penjemput" class="w-full max-h-72 object-contain mx-auto">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
            <button type="button" data-close-modal="modal-detail"
                    class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#1B1B1B] text-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                Tutup
            </button>
        </div>
    </dialog>

    {{-- Modal Konfirmasi Pembatalan (<dialog> header coral) --}}
    <dialog id="modal-cancel" class="ta-modal p-0 border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(480px,calc(100vw-32px))] overflow-hidden">
        <form id="form-cancel-penjemputan" method="POST" action="">
            @csrf
            @method('PATCH')

            <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FF9E80] border-b-[1.5px] border-[#1B1B1B]">
                <h3 class="text-lg font-bold m-0">Batalkan Penjemputan?</h3>
                <button type="button" data-close-modal="modal-cancel" aria-label="Tutup"
                        class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="p-5 space-y-3 text-sm">
                <p class="m-0">
                    Anda yakin ingin membatalkan pengajuan penjemputan untuk <b id="mc-anak">Anak</b> oleh <b id="mc-penjemput">Penjemput</b>?
                </p>
                <p class="text-xs text-[#6B6B6B] m-0">
                    Pengajuan yang dibatalkan tidak akan diproses oleh pengasuh. Anda dapat membuat pengajuan baru kapan saja jika diperlukan.
                </p>
            </div>

            <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                <button type="button" data-close-modal="modal-cancel"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Kembali
                </button>
                <button type="submit"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('modal-detail');
    const cancelModal = document.getElementById('modal-cancel');

    // Buka modal detail
    document.querySelectorAll('[data-detail-btn]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('md-anak').textContent = btn.dataset.anak || '-';
            document.getElementById('md-status').textContent = btn.dataset.status || '-';
            document.getElementById('md-penjemput').textContent = btn.dataset.penjemput || '-';
            document.getElementById('md-hubungan-hp').textContent =
                (btn.dataset.hubungan || '-') + ' · ' + (btn.dataset.hp || '-');
            document.getElementById('md-nik').textContent = btn.dataset.nik || '-';
            document.getElementById('md-waktu').textContent = btn.dataset.waktu || '-';
            document.getElementById('md-metode').textContent = btn.dataset.metode || '-';
            document.getElementById('md-catatan').textContent = btn.dataset.catatan || '-';

            const fotoWrap = document.getElementById('md-foto-wrap');
            const fotoImg = document.getElementById('md-foto-img');
            if (btn.dataset.foto) {
                fotoImg.src = btn.dataset.foto;
                fotoWrap.classList.remove('hidden');
            } else {
                fotoImg.src = '';
                fotoWrap.classList.add('hidden');
            }

            if (detailModal && detailModal.showModal) {
                detailModal.showModal();
            }
        });
    });

    // Buka modal konfirmasi pembatalan
    document.querySelectorAll('[data-cancel-btn]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById('form-cancel-penjemputan');
            form.action = btn.dataset.action;
            document.getElementById('mc-anak').textContent = btn.dataset.anak || 'Anak';
            document.getElementById('mc-penjemput').textContent = btn.dataset.penjemput || 'Penjemput';

            if (cancelModal && cancelModal.showModal) {
                cancelModal.showModal();
            }
        });
    });

    // Tutup modal dari tombol close atau klik backdrop
    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(btn.dataset.closeModal);
            if (target) target.close();
        });
    });

    [detailModal, cancelModal].forEach(function (dialog) {
        if (!dialog) return;
        dialog.addEventListener('click', function (e) {
            if (e.target === dialog) {
                dialog.close();
            }
        });
    });
});
</script>
@endpush