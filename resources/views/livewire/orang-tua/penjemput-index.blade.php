<div class="space-y-6">
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
            <button type="button"
                    wire:click="toggleForm"
                    aria-expanded="{{ $showForm ? 'true' : 'false' }}"
                    class="inline-flex items-center justify-center gap-2 font-semibold text-sm px-5 py-3 rounded-[10px] bg-[#FFD43B] text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                <span>{{ $showForm ? '✕ Tutup Formulir' : '+ Ajukan Penjemputan' }}</span>
            </button>
        </div>
    </header>

    {{-- Flash Message --}}
    @if (session('success'))
        <div role="status" class="flex items-center gap-3 p-4 rounded-[10px] bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] text-sm font-semibold">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div role="alert" class="flex items-center gap-3 p-4 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] text-sm font-semibold">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- PANEL TERPADU: FORMULIR PENGAJUAN PENJEMPUTAN --}}
    @if ($showForm)
        <section aria-label="Formulir Pengajuan Penjemputan"
                 class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b-[1.5px] border-[#1B1B1B]">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#FFD43B] border border-[#1B1B1B]">
                        Formulir Baru
                    </span>
                    <h2 class="text-lg sm:text-xl font-bold m-0">Ajukan Izin Penjemputan</h2>
                </div>
                <button type="button"
                        wire:click="toggleForm"
                        class="self-start sm:self-auto text-xs font-semibold px-3 py-1.5 rounded-[8px] bg-white border border-[#1B1B1B] cursor-pointer">
                    Sembunyikan Form
                </button>
            </div>

            @if ($errors->any())
                <div role="alert" class="flex items-start gap-3 p-4 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] text-sm">
                    <span class="text-base leading-none">⚠️</span>
                    <div>
                        <div class="font-bold">Periksa kembali isian formulir Anda:</div>
                        <ul class="list-disc pl-5 mt-1 space-y-0.5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if ($anakAktifBisaJemput->isEmpty())
                <div class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-6 text-center space-y-2">
                    <span class="inline-grid place-items-center w-12 h-12 rounded-full bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] text-xl">
                        🧸
                    </span>
                    <h3 class="text-base font-bold">Belum ada anak aktif yang dapat diajukan penjemputan</h3>
                    <p class="text-xs text-[#6B6B6B] max-w-md mx-auto">
                        Pengajuan hanya tersedia untuk anak berstatus aktif dan belum tercatat check-out hari ini.
                    </p>
                </div>
            @else
                <form wire:submit="simpanPengajuan" class="space-y-5">
                    {{-- Langkah 1: Pilih Anak --}}
                    <div class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 sm:p-5 space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="inline-grid place-items-center w-8 h-8 rounded-full bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                                1
                            </span>
                            <div>
                                <h3 class="text-base font-bold leading-tight">Pilih Anak yang Akan Dijemput</h3>
                                <p class="text-xs text-[#6B6B6B]">Pilih salah satu anak Anda yang berstatus aktif.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($anakAktifBisaJemput as $anak)
                                @php
                                    $sedangDiajukan = in_array($anak->id, $anakSedangDiajukanIds, true);
                                    $infoMedis = collect([
                                        $anak->catatanKesehatan?->alergi,
                                        $anak->catatanKesehatan?->pantangan_makanan,
                                        $anak->catatanKesehatan?->catatan,
                                    ])->filter()->implode(' · ');
                                @endphp
                                <label wire:key="pilih-anak-{{ $anak->id }}" class="block cursor-pointer">
                                    <input type="radio"
                                           wire:model.live="anak_id"
                                           value="{{ $anak->id }}"
                                           class="peer sr-only">

                                    <div class="h-full p-3.5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white peer-checked:bg-[#A8E6CF] transition-colors space-y-2">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <div class="font-bold text-sm">{{ $anak->nama_lengkap }}</div>
                                                <div class="text-xs text-[#6B6B6B]">
                                                    Kelompok {{ $anak->kelompokUsia?->nama ?? 'Umum' }}
                                                </div>
                                            </div>
                                            <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-white border border-[#1B1B1B]">
                                                {{ $anak->status_kehadiran_label }}
                                            </span>
                                        </div>

                                        @if ($infoMedis !== '')
                                            <div class="text-xs bg-[#FFF6D6] border border-[#1B1B1B] rounded-[8px] px-2.5 py-1.5">
                                                <b>Kesehatan:</b> {{ $infoMedis }}
                                            </div>
                                        @endif

                                        @if ($sedangDiajukan)
                                            <div class="text-xs font-semibold px-2.5 py-1 rounded-[8px] bg-[#FF9E80] border border-[#1B1B1B]">
                                                ⚠️ Sudah memiliki pengajuan berstatus menunggu
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('anak_id')
                            <p class="text-xs font-semibold text-[#B02E12]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Langkah 2: Identitas Wali Penjemput --}}
                    <div class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 sm:p-5 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-grid place-items-center w-8 h-8 rounded-full bg-[#A8D4FF] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                                2
                            </span>
                            <div>
                                <h3 class="text-base font-bold leading-tight">Identitas Wali Penjemput</h3>
                                <p class="text-xs text-[#6B6B6B]">Pilih wali dari daftar terdaftar atau daftarkan penjemput baru.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2.5" role="radiogroup" aria-label="Tipe Penjemput">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="mode_penjemput" value="baru" class="peer sr-only">
                                <span class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold px-4 py-2 rounded-full border-[1.5px] border-[#1B1B1B] bg-white peer-checked:bg-[#1B1B1B] peer-checked:text-white transition-colors">
                                    ➕ Input Penjemput Baru
                                </span>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="mode_penjemput" value="terdaftar" class="peer sr-only">
                                <span class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold px-4 py-2 rounded-full border-[1.5px] border-[#1B1B1B] bg-white peer-checked:bg-[#1B1B1B] peer-checked:text-white transition-colors">
                                    📋 Pilih Wali Terdaftar
                                </span>
                            </label>
                        </div>

                        @if ($mode_penjemput === 'terdaftar')
                            <div class="space-y-2">
                                <label for="penjemput_id" class="block text-sm font-semibold">Pilih Wali Penjemput Terdaftar</label>
                                <select id="penjemput_id"
                                        wire:model="penjemput_id"
                                        class="w-full px-3.5 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6]">
                                    <option value="">-- {{ $anak_id ? 'Pilih wali penjemput' : 'Pilih anak terlebih dahulu' }} --</option>
                                    @foreach ($waliAnakTerpilih as $wali)
                                        <option value="{{ $wali->id }}">
                                            {{ $wali->nama_lengkap }} ({{ ucfirst($wali->hubungan) }} · {{ $wali->no_hp }})
                                        </option>
                                    @endforeach
                                </select>
                                @if ($anak_id && $waliAnakTerpilih->isEmpty())
                                    <p class="text-xs text-[#6B6B6B]">
                                        Anak terpilih belum memiliki wali penjemput terdaftar. Gunakan opsi <b>Input Penjemput Baru</b>.
                                    </p>
                                @endif
                                @error('penjemput_id')
                                    <p class="text-xs font-semibold text-[#B02E12]">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="space-y-1.5">
                                    <label for="nama" class="block text-sm font-semibold">Nama Lengkap <span class="text-[#B02E12]">*</span></label>
                                    <input id="nama"
                                           type="text"
                                           wire:model="nama"
                                           placeholder="Sesuai KTP"
                                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                                    @error('nama') <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label for="hubungan" class="block text-sm font-semibold">Hubungan <span class="text-[#B02E12]">*</span></label>
                                    <input id="hubungan"
                                           type="text"
                                           wire:model="hubungan"
                                           list="daftar-hubungan"
                                           placeholder="Ayah, Ibu, Bibi, Nenek..."
                                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                                    <datalist id="daftar-hubungan">
                                        <option value="Ayah">
                                        <option value="Ibu">
                                        <option value="Nenek">
                                        <option value="Kakek">
                                        <option value="Bibi">
                                        <option value="Paman">
                                        <option value="Kakak">
                                    </datalist>
                                    @error('hubungan') <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label for="no_hp" class="block text-sm font-semibold">No. HP / WhatsApp <span class="text-[#B02E12]">*</span></label>
                                    <input id="no_hp"
                                           type="tel"
                                           wire:model="no_hp"
                                           placeholder="081234567890"
                                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                                    @error('no_hp') <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span> @enderror
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="inline-flex items-center gap-2 text-xs font-semibold cursor-pointer">
                                        <input type="checkbox" wire:model="simpan_wali" class="w-4 h-4 accent-[#1B1B1B]">
                                        <span>Simpan otomatis ke daftar wali penjemput terdaftar untuk pengajuan berikutnya</span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Langkah 3: Tanggal, Foto Identitas & Alasan --}}
                    <div class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 sm:p-5 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-grid place-items-center w-8 h-8 rounded-full bg-[#CDBDFF] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                                3
                            </span>
                            <div>
                                <h3 class="text-base font-bold leading-tight">Jadwal, Foto Identitas &amp; Alasan Penjemputan</h3>
                                <p class="text-xs text-[#6B6B6B]">Unggah foto KTP/identitas dan tuliskan keterangan penjemputan untuk pengasuh.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="tanggal_jemput" class="block text-sm font-semibold">Tanggal Penjemputan <span class="text-[#B02E12]">*</span></label>
                                <input id="tanggal_jemput"
                                       type="date"
                                       wire:model="tanggal_jemput"
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                                @error('tanggal_jemput') <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="foto_bukti" class="block text-sm font-semibold">
                                    Foto Identitas / KTP
                                    @if ($mode_penjemput === 'baru')
                                        <span class="text-[#B02E12]">*</span>
                                    @else
                                        <span class="text-xs font-normal text-[#6B6B6B]">(Opsional jika memakai foto terdaftar)</span>
                                    @endif
                                </label>
                                <input id="foto_bukti"
                                       type="file"
                                       wire:model="foto_bukti"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="w-full px-3 py-2 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white file:mr-3 file:py-1 file:px-3 file:rounded-[8px] file:border file:border-[#1B1B1B] file:bg-[#FFD43B] file:font-semibold file:text-xs cursor-pointer">
                                <span wire:loading wire:target="foto_bukti" class="block text-xs font-semibold text-[#1B1B1B]">
                                    Mengunggah pratinjau foto...
                                </span>
                                <span class="block text-xs text-[#6B6B6B]">JPG, PNG, atau WEBP maksimal 2 MB.</span>
                                @error('foto_bukti') <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span> @enderror
                            </div>

                            @if ($foto_bukti && method_exists($foto_bukti, 'temporaryUrl'))
                                <div class="sm:col-span-2">
                                    <div class="inline-block border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6] p-2">
                                        <img src="{{ $foto_bukti->temporaryUrl() }}" alt="Pratinjau foto identitas" class="max-h-40 rounded-[8px] object-contain">
                                    </div>
                                </div>
                            @endif

                            <div class="sm:col-span-2 space-y-1.5">
                                <label for="alasan" class="block text-sm font-semibold">Alasan &amp; Catatan Penjemputan <span class="text-[#B02E12]">*</span></label>
                                <textarea id="alasan"
                                          wire:model="alasan"
                                          rows="2"
                                          placeholder="Contoh: Orang tua lembur, anak dijemput oleh bibi sekitar pukul 16.30 WIB."
                                          class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]"></textarea>
                                @error('alasan') <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3">
                        <button type="button"
                                wire:click="toggleForm"
                                class="inline-flex items-center justify-center font-semibold text-sm px-5 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="simpanPengajuan,foto_bukti"
                                class="inline-flex items-center justify-center font-semibold text-sm px-6 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer disabled:opacity-60">
                            <span wire:loading.remove wire:target="simpanPengajuan">Kirim Pengajuan Penjemputan</span>
                            <span wire:loading wire:target="simpanPengajuan">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            @endif
        </section>
    @endif

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
        <div class="bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 text-center">
            <div class="text-3xl sm:text-4xl font-bold leading-none">{{ $statistik['ditolak'] + $statistik['dibatalkan'] }}</div>
            <div class="text-sm font-bold mt-2">Ditolak / Batal</div>
        </div>
    </section>

    {{-- Status Anak Terdaftar --}}
    <section aria-label="Status Kehadiran Anak" class="space-y-3">
        <h2 class="text-lg font-bold">Anak Terdaftar Anda</h2>

        @if ($daftarAnak->isEmpty())
            <div class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-6 text-center">
                <p class="font-bold text-base">Belum ada data anak pada akun Anda.</p>
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
                        $inisialAnak = collect(explode(' ', $anak->nama_lengkap))
                            ->map(fn ($w) => mb_substr($w, 0, 1))
                            ->take(2)
                            ->implode('');
                        $catatanMedis = collect([
                            $anak->catatanKesehatan?->riwayat_medis,
                            $anak->catatanKesehatan?->alergi,
                            $anak->catatanKesehatan?->catatan,
                        ])->filter()->implode(' · ');
                    @endphp
                    <div wire:key="kartu-anak-{{ $anak->id }}" class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 flex flex-col justify-between gap-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <span class="inline-grid place-items-center w-12 h-12 rounded-full bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] font-bold text-lg shrink-0">
                                    {{ $inisialAnak }}
                                </span>
                                <div class="min-w-0">
                                    <div class="font-bold text-base truncate">{{ $anak->nama_lengkap }}</div>
                                    <div class="text-xs text-[#6B6B6B]">
                                        ID #{{ $anak->id }} · Kelompok {{ $anak->kelompokUsia?->nama ?? 'Belum Ditentukan' }}
                                    </div>
                                </div>
                            </div>

                            <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full border border-[#1B1B1B] shrink-0 {{ $toneBadge }}">
                                {{ $anak->status_kehadiran_label }}
                            </span>
                        </div>

                        @if ($catatanMedis !== '')
                            <div class="text-xs bg-[#FFF6D6] border border-[#1B1B1B] rounded-[10px] px-3 py-2">
                                <span class="font-bold">Catatan Kesehatan:</span> {{ $catatanMedis }}
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-[#1B1B1B]">
                            <span class="text-xs text-[#6B6B6B]">
                                Wali terdaftar: <b class="text-[#1B1B1B]">{{ $anak->penjemputTerdaftar->count() }} orang</b>
                            </span>

                            @if ($anak->bisa_diajukan)
                                <button type="button"
                                        wire:click="toggleForm({{ $anak->id }})"
                                        class="inline-flex items-center font-semibold text-xs px-3.5 py-2 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer text-[#1B1B1B]">
                                    Ajukan Jemput
                                </button>
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
        <div class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 space-y-3">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm pointer-events-none">🔍</span>
                    <input type="search"
                           wire:model.live.debounce.300ms="q"
                           placeholder="Cari nama anak atau nama penjemput..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6]">
                </div>

                <select wire:model.live="filterAnakId"
                        aria-label="Filter berdasarkan anak"
                        class="px-3.5 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none">
                    <option value="">Semua Anak</option>
                    @foreach ($daftarAnak as $anak)
                        <option value="{{ $anak->id }}">{{ $anak->nama_lengkap }}</option>
                    @endforeach
                </select>

                @if ($q !== '' || $filterAnakId !== '' || $filterStatus !== '')
                    <button type="button"
                            wire:click="resetFilter"
                            class="inline-flex items-center justify-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] cursor-pointer text-[#1B1B1B]">
                        Reset
                    </button>
                @endif
            </div>

            {{-- Chip Filter Cepat Status --}}
            <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter status pengajuan">
                <button type="button"
                        wire:click="setFilterStatus('')"
                        class="text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#1B1B1B] cursor-pointer text-[#1B1B1B] {{ $filterStatus === '' ? 'bg-[#FFD43B] border-[1.5px]' : 'bg-white hover:bg-[#FFF6D6]' }}">
                    Semua
                </button>
                @foreach ($daftarStatus as $kodeStatus => $labelStatus)
                    <button type="button"
                            wire:click="setFilterStatus('{{ $kodeStatus }}')"
                            class="text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#1B1B1B] cursor-pointer text-[#1B1B1B] {{ $filterStatus === $kodeStatus ? 'bg-[#FFD43B] border-[1.5px]' : 'bg-white hover:bg-[#FFF6D6]' }}">
                        {{ $labelStatus }}
                    </button>
                @endforeach
            </div>
        </div>

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
                    <button type="button"
                            wire:click="toggleForm"
                            class="inline-flex items-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] mt-4 cursor-pointer text-[#1B1B1B]">
                        + Buat Pengajuan Pertama
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-[#FFD43B] border-b-[1.5px] border-[#1B1B1B] text-left font-bold">
                                <th class="py-3 px-4">Anak</th>
                                <th class="py-3 px-4">Penjemput</th>
                                <th class="py-3 px-4">Tanggal Jemput</th>
                                <th class="py-3 px-4">Alasan / Keterangan</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1B1B1B]">
                            @foreach ($riwayatPenjemputan as $item)
                                @php
                                    $statusBadgeClass = match ($item->status) {
                                        'menunggu'   => 'bg-[#FFD43B] text-[#1B1B1B]',
                                        'disetujui'  => 'bg-[#A8E6CF] text-[#1B1B1B]',
                                        'ditolak'    => 'bg-[#FF9E80] text-[#1B1B1B]',
                                        default      => 'bg-[#1B1B1B] text-white',
                                    };
                                @endphp
                                <tr wire:key="riwayat-{{ $item->id }}" class="even:bg-[#FFF6D6]">
                                    <td class="py-3.5 px-4 align-top">
                                        <div class="font-bold">{{ $item->anak?->nama_lengkap ?? '-' }}</div>
                                        <div class="text-xs text-[#6B6B6B]">
                                            Kelompok {{ $item->anak?->kelompokUsia?->nama ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 align-top">
                                        <div class="font-bold">{{ $item->nama_penjemput }}</div>
                                        <div class="text-xs text-[#6B6B6B] capitalize">
                                            {{ $item->hubungan_penjemput ?? 'Wali' }}
                                            @if ($item->no_hp_penjemput)
                                                · {{ $item->no_hp_penjemput }}
                                            @endif
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                        <div class="font-semibold">{{ $item->tanggal_jemput?->translatedFormat('d M Y') }}</div>
                                        <div class="text-xs text-[#6B6B6B]">Diajukan {{ $item->created_at?->format('H:i') }}</div>
                                    </td>

                                    <td class="py-3.5 px-4 align-top">
                                        <p class="text-xs text-[#1B1B1B] max-w-[240px] line-clamp-2 m-0" title="{{ $item->alasan }}">
                                            {{ $item->alasan }}
                                        </p>
                                    </td>

                                    <td class="py-3.5 px-4 align-top">
                                        <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full border border-[#1B1B1B] capitalize {{ $statusBadgeClass }}">
                                            {{ $item->status }}
                                        </span>
                                        @if ($item->validator)
                                            <p class="text-xs text-[#6B6B6B] mt-1 m-0">
                                                Oleh: {{ $item->validator->nama }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 align-top text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <button type="button"
                                                    wire:click="bukaDetail({{ $item->id }})"
                                                    class="inline-flex items-center font-semibold text-xs px-3 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                                Detail
                                            </button>

                                            @if ($item->status === 'menunggu')
                                                <button type="button"
                                                        wire:click="konfirmasiBatal({{ $item->id }})"
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

    {{-- Daftar Wali Penjemput Terdaftar --}}
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
                    @php
                        $inisialWali = collect(explode(' ', $wali->nama_lengkap))
                            ->map(fn ($w) => mb_substr($w, 0, 1))
                            ->take(2)
                            ->implode('');
                    @endphp
                    <div wire:key="wali-card-{{ $wali->id }}" class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 flex items-start gap-3.5">
                        <span class="inline-grid place-items-center w-11 h-11 rounded-full bg-[#CDBDFF] border-[1.5px] border-[#1B1B1B] font-bold text-base shrink-0">
                            {{ $inisialWali }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <div class="font-bold text-sm truncate">{{ $wali->nama_lengkap }}</div>
                                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-[#A8E6CF] border border-[#1B1B1B] capitalize shrink-0">
                                    {{ $wali->hubungan }}
                                </span>
                            </div>
                            <div class="text-xs text-[#6B6B6B] mt-1">
                                Anak: <b class="text-[#1B1B1B]">{{ $wali->anak?->nama_lengkap ?? '-' }}</b>
                            </div>
                            <div class="text-xs text-[#6B6B6B]">HP: {{ $wali->no_hp }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Modal Detail Penjemputan --}}
    @if ($detailItem)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="tutupDetail">
            <div role="dialog" aria-modal="true" class="border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(560px,calc(100vw-32px))] max-h-[calc(100vh-48px)] overflow-hidden flex flex-col">
                <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FFD43B] border-b-[1.5px] border-[#1B1B1B]">
                    <h3 class="text-lg font-bold m-0">Detail Pengajuan Penjemputan</h3>
                    <button type="button" wire:click="tutupDetail" aria-label="Tutup"
                            class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="p-5 overflow-y-auto space-y-4 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4">
                        <div>
                            <div class="text-xs text-[#6B6B6B]">Nama Anak</div>
                            <div class="font-bold">{{ $detailItem['anak'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#6B6B6B]">Status Pengajuan</div>
                            <div class="font-bold">{{ $detailItem['status'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#6B6B6B]">Nama Penjemput</div>
                            <div class="font-bold">{{ $detailItem['penjemput'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#6B6B6B]">Hubungan &amp; No. HP</div>
                            <div class="font-semibold">{{ $detailItem['hubungan'] }} · {{ $detailItem['hp'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#6B6B6B]">Tanggal Penjemputan</div>
                            <div class="font-semibold">{{ $detailItem['waktu'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#6B6B6B]">Divalidasi Oleh</div>
                            <div class="font-semibold">{{ $detailItem['validator'] }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold mb-1">Alasan &amp; Catatan Orang Tua</div>
                        <div class="p-3 rounded-[10px] border border-[#1B1B1B] bg-white text-xs">{{ $detailItem['alasan'] }}</div>
                    </div>

                    @if ($detailItem['foto'] !== '')
                        <div class="space-y-2">
                            <div class="text-xs font-bold">Foto Identitas Penjemput</div>
                            <div class="border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6]">
                                <img src="{{ $detailItem['foto'] }}" alt="Foto identitas penjemput" class="w-full max-h-72 object-contain mx-auto">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                    <button type="button" wire:click="tutupDetail"
                            class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#1B1B1B] text-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Pembatalan --}}
    @if ($cancelTarget)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="tutupBatal">
            <div role="dialog" aria-modal="true" class="border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(480px,calc(100vw-32px))] overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FF9E80] border-b-[1.5px] border-[#1B1B1B]">
                    <h3 class="text-lg font-bold m-0">Batalkan Penjemputan?</h3>
                    <button type="button" wire:click="tutupBatal" aria-label="Tutup"
                            class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="p-5 space-y-3 text-sm">
                    <p class="m-0">
                        Anda yakin ingin membatalkan pengajuan penjemputan untuk <b>{{ $cancelTarget['anak'] }}</b> oleh <b>{{ $cancelTarget['penjemput'] }}</b>?
                    </p>
                    <p class="text-xs text-[#6B6B6B] m-0">
                        Pengajuan yang dibatalkan tidak akan diproses oleh pengasuh. Anda dapat membuat pengajuan baru kapan saja.
                    </p>
                </div>

                <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                    <button type="button" wire:click="tutupBatal"
                            class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                        Kembali
                    </button>
                    <button type="button" wire:click="batalkanPengajuan"
                            class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                        Ya, Batalkan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>