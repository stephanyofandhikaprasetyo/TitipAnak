@extends('layouts.pengasuh')

@section('title', 'Buat Laporan Harian Anak')

@section('content')
    {{-- Header Halaman --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#A8D4FF] border border-[#1B1B1B]">
                    Pengasuh
                </span>
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#FFD43B] border border-[#1B1B1B]">
                    Formulir Laporan Harian
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Buat Laporan Harian Anak</h1>
            <p class="text-sm text-[#6B6B6B] mt-1">
                Isi rincian makan, tidur, BAB/BAK, aktivitas, kondisi anak, serta unggah bukti foto kegiatan hari ini.
            </p>
        </div>

        <a href="{{ route('pengasuh.laporan-harian.index') }}"
           class="inline-flex items-center justify-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
            ← Kembali ke Daftar
        </a>
    </header>

    {{-- Alert Validasi Global --}}
    @if ($errors->any())
        <div role="alert" class="flex items-start gap-3 p-4 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] text-sm">
            <span class="text-base leading-none">⚠️</span>
            <div>
                <div class="font-bold">Periksa kembali isian formulir laporan harian:</div>
                <ul class="list-disc pl-5 mt-1 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ route('pengasuh.laporan-harian.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf

        {{-- Bagian 1: Identitas Anak & Tanggal --}}
        <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#A8D4FF] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    1
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Pilih Anak &amp; Tanggal Laporan</h2>
                    <p class="text-xs text-[#6B6B6B]">Satu anak memiliki satu laporan harian per tanggal.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="anak_id" class="block text-sm font-semibold">Nama Anak <span class="text-[#B02E12]">*</span></label>
                    <select id="anak_id"
                            name="anak_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('anak_id') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                        <option value="">-- Pilih anak --</option>
                        @foreach ($daftarAnak as $anak)
                            @php
                                $sudahHariIni = in_array($anak->id, $sudahLaporanHariIniIds, true);
                            @endphp
                            <option value="{{ $anak->id }}"
                                    data-medis="{{ $anak->catatan_medis ?? '' }}"
                                    @selected((int) old('anak_id', $selectedAnakId) === $anak->id)>
                                {{ $anak->nama_lengkap }} ({{ $anak->kode_anak }}) {{ $sudahHariIni ? '— [Sudah ada laporan hari ini]' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('anak_id')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="tanggal" class="block text-sm font-semibold">Tanggal Laporan <span class="text-[#B02E12]">*</span></label>
                    <input id="tanggal"
                           type="date"
                           name="tanggal"
                           max="{{ now()->format('Y-m-d') }}"
                           value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('tanggal') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                    @error('tanggal')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Info Alergi / Catatan Medis Anak --}}
            <div id="info-medis-box" class="hidden p-3.5 rounded-[10px] bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] text-xs">
                <span class="font-bold">⚠️ Catatan Medis / Alergi Anak:</span>
                <span id="info-medis-text"></span>
            </div>
        </section>

        {{-- Bagian 2: Rincian Harian (Makan, Tidur, BAB/BAK, Aktivitas, Kondisi) --}}
        <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    2
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Catatan Harian &amp; Kondisi Anak</h2>
                    <p class="text-xs text-[#6B6B6B]">Lengkapi seluruh indikator harian anak selama berada di daycare.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="kondisi" class="block text-sm font-semibold">Kondisi Umum Anak <span class="text-[#B02E12]">*</span></label>
                    <select id="kondisi"
                            name="kondisi"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('kondisi') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                        @foreach ($daftarKondisi as $kode => $label)
                            <option value="{{ $kode }}" @selected(old('kondisi', 'sehat') === $kode)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('kondisi')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="tidur" class="block text-sm font-semibold">Catatan Tidur / Istirahat <span class="text-[#B02E12]">*</span></label>
                    <input id="tidur"
                           type="text"
                           name="tidur"
                           value="{{ old('tidur') }}"
                           placeholder="Contoh: Tidur siang nyenyak pukul 12.30 - 14.15 WIB"
                           required
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('tidur') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                    @error('tidur')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="bab_bak" class="block text-sm font-semibold">Catatan BAB &amp; BAK <span class="text-[#B02E12]">*</span></label>
                    <input id="bab_bak"
                           type="text"
                           name="bab_bak"
                           value="{{ old('bab_bak') }}"
                           placeholder="Contoh: BAK 3x normal, BAB 1x pukul 10.15 (konsistensi normal)"
                           required
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('bab_bak') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                    @error('bab_bak')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="makan" class="block text-sm font-semibold">Catatan Makan &amp; Minum <span class="text-[#B02E12]">*</span></label>
                    <textarea id="makan"
                              name="makan"
                              rows="2"
                              placeholder="Contoh: Sarapan bubur ayam habis 1 porsi, snack buah pisang, makan siang nasi sop ayam habis."
                              required
                              class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('makan') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">{{ old('makan') }}</textarea>
                    @error('makan')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label for="aktivitas" class="block text-sm font-semibold">Aktivitas &amp; Perkembangan Hari Ini <span class="text-[#B02E12]">*</span></label>
                    <textarea id="aktivitas"
                              name="aktivitas"
                              rows="3"
                              placeholder="Contoh: Mengikuti senam pagi, bermain balok susun bersama teman, dan mewarnai gambar hewan dengan antusias."
                              required
                              class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('aktivitas') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">{{ old('aktivitas') }}</textarea>
                    @error('aktivitas')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label for="catatan_tambahan" class="block text-sm font-semibold">Catatan Tambahan untuk Orang Tua (Opsional)</label>
                    <textarea id="catatan_tambahan"
                              name="catatan_tambahan"
                              rows="2"
                              placeholder="Contoh: Mohon besok membawakan baju ganti ekstra dan popok cadangan."
                              class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('catatan_tambahan') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">{{ old('catatan_tambahan') }}</textarea>
                    @error('catatan_tambahan')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Bagian 3: Unggah Foto Kegiatan (Stage 6) --}}
        <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    3
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Unggah Foto Kegiatan (Opsional, Maks. 5 Foto)</h2>
                    <p class="text-xs text-[#6B6B6B]">Berikan bukti visual kegiatan anak agar orang tua merasa tenang.</p>
                </div>
            </div>

            <div class="space-y-2">
                <label for="foto_kegiatan" class="block text-sm font-semibold">Pilih Foto Kegiatan</label>
                <input id="foto_kegiatan"
                       type="file"
                       name="foto_kegiatan[]"
                       multiple
                       accept="image/jpeg,image/png,image/webp"
                       class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-[8px] file:border file:border-[#1B1B1B] file:bg-[#FFD43B] file:font-semibold file:text-xs cursor-pointer">
                <span class="block text-xs text-[#6B6B6B]">
                    Dapat memilih hingga 5 foto sekaligus (JPG, PNG, WEBP · maks. 2 MB per foto). Foto juga bisa ditambah atau diganti setelah laporan disimpan.
                </span>

                @error('foto_kegiatan')
                    <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                @enderror
                @error('foto_kegiatan.*')
                    <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                @enderror
            </div>

            <div id="preview-kegiatan-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 hidden"></div>
        </section>

        {{-- Tombol Aksi --}}
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('pengasuh.laporan-harian.index') }}"
               class="inline-flex items-center justify-center font-semibold text-sm px-5 py-3 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center font-semibold text-sm px-6 py-3 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                Simpan Laporan Harian
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAnak = document.getElementById('anak_id');
    const medisBox   = document.getElementById('info-medis-box');
    const medisText  = document.getElementById('info-medis-text');
    const inputFoto  = document.getElementById('foto_kegiatan');
    const gridPrev   = document.getElementById('preview-kegiatan-grid');

    function syncMedis() {
        if (!selectAnak || !medisBox || !medisText) return;
        const opt   = selectAnak.options[selectAnak.selectedIndex];
        const medis = opt ? (opt.dataset.medis || '').trim() : '';
        if (medis) {
            medisText.textContent = ' ' + medis;
            medisBox.classList.remove('hidden');
        } else {
            medisBox.classList.add('hidden');
        }
    }

    if (selectAnak) {
        selectAnak.addEventListener('change', syncMedis);
        syncMedis();
    }

    if (inputFoto && gridPrev) {
        inputFoto.addEventListener('change', function () {
            gridPrev.innerHTML = '';
            const files = Array.from(inputFoto.files || []);
            if (files.length === 0) {
                gridPrev.classList.add('hidden');
                return;
            }

            gridPrev.classList.remove('hidden');
            files.forEach(function (file, idx) {
                const wrap = document.createElement('div');
                wrap.className = 'border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6] p-2.5 space-y-2';

                const img = document.createElement('img');
                img.className = 'w-full h-36 object-cover rounded-[8px] border border-[#1B1B1B] bg-white';
                img.alt = 'Pratinjau foto ' + (idx + 1);

                const reader = new FileReader();
                reader.onload = function (e) { img.src = e.target.result; };
                reader.readAsDataURL(file);

                const inputKet = document.createElement('input');
                inputKet.type = 'text';
                inputKet.name = 'keterangan_foto[' + idx + ']';
                inputKet.placeholder = 'Keterangan foto #' + (idx + 1) + ' (opsional)';
                inputKet.className = 'w-full px-2.5 py-1.5 text-xs bg-white border border-[#1B1B1B] rounded-[8px]';

                wrap.appendChild(img);
                wrap.appendChild(inputKet);
                gridPrev.appendChild(wrap);
            });
        });
    }
});
</script>
@endpush