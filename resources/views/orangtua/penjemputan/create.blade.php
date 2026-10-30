@extends('layouts.app')

@section('title', 'Ajukan Izin Penjemputan')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- ================= HEADER ================= --}}
    <header class="mb-8">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="inline-flex items-center text-xs font-semibold px-3 py-1.5 border-[1.5px] border-ink rounded-full bg-mint text-ink">
                Orang Tua
            </span>
            <span class="inline-flex items-center text-xs font-semibold px-3 py-1.5 border-[1.5px] border-ink rounded-full bg-yellow text-ink">
                Izin Penjemputan
            </span>
        </div>
        <h1 class="text-3xl font-bold text-ink">Ajukan Izin Penjemputan</h1>
        <p class="text-muted mt-2 max-w-prose">
            Ajukan izin penjemputan darurat atau sementara untuk anak Anda.
            Pengajuan akan diverifikasi oleh pengasuh / admin sebelum berlaku.
        </p>
    </header>

    {{-- ================= FLASH MESSAGES ================= --}}
    @if (session('success'))
        <div class="flex gap-3 p-4 border-[1.5px] border-ink rounded-ta bg-mint mb-6">
            <span aria-hidden="true">✅</span>
            <div class="text-sm"><b>Berhasil.</b> {{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex gap-3 p-4 border-[1.5px] border-ink rounded-ta bg-coral mb-6">
            <span aria-hidden="true">⚠️</span>
            <div class="text-sm">
                <b>Gagal.</b> Periksa kembali isian formulir di bawah.
                @if ($errors->has('general'))
                    <div class="mt-1">{{ $errors->first('general') }}</div>
                @endif
            </div>
        </div>
    @endif

    {{-- ================= EMPTY STATE ================= --}}
    @if ($anakList->isEmpty())
        <div class="border-[1.5px] border-ink rounded-ta p-8 bg-cream text-center">
            <div class="text-4xl mb-2" aria-hidden="true">👶</div>
            <div class="text-lg font-bold text-ink">Belum ada data anak aktif</div>
            <p class="text-sm text-muted mt-1">
                Silakan hubungi admin untuk mendaftarkan anak Anda terlebih dahulu.
            </p>
        </div>
    @else

    {{-- ================= FORM ================= --}}
    <form action="{{ route('orangtua.penjemputan.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-white border-[1.5px] border-ink rounded-ta p-6">

        @csrf

        {{-- ---------- Section 1: Data Anak ---------- --}}
        <div class="flex items-center gap-3 mb-4">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border-[1.5px] border-ink font-bold text-ink">1</span>
            <h2 class="text-lg font-bold text-ink">Data Anak</h2>
        </div>

        <div class="mb-8">
            <label for="anak_id" class="block text-sm font-semibold mb-1.5">Pilih Anak</label>
            <select id="anak_id"
                    name="anak_id"
                    class="w-full border-[1.5px] rounded-ta px-3.5 py-2.5 bg-white text-ink focus:outline-none focus:ring-2 focus:ring-yellow
                           {{ $errors->has('anak_id') ? 'border-red-500 bg-red-50' : 'border-ink' }}">
                <option value="">— Pilih anak —</option>
                @foreach ($anakList as $anak)
                    <option value="{{ $anak->id }}" @selected(old('anak_id') == $anak->id)>
                        {{ $anak->nama_lengkap }} ({{ $anak->kode_anak }})@if ($anak->kelas) — {{ $anak->kelas->nama_kelas }}@endif
                    </option>
                @endforeach
            </select>
            @error('anak_id')
                <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- ---------- Section 2: Data Penjemput ---------- --}}
        <div class="flex items-center gap-3 mb-4">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border-[1.5px] border-ink font-bold text-ink">2</span>
            <h2 class="text-lg font-bold text-ink">Data Penjemput</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            {{-- Nama --}}
            <div>
                <label for="nama_penjemput" class="block text-sm font-semibold mb-1.5">Nama Lengkap Penjemput</label>
                <input id="nama_penjemput" type="text" name="nama_penjemput"
                       value="{{ old('nama_penjemput') }}"
                       placeholder="Sesuai KTP"
                       class="w-full border-[1.5px] rounded-ta px-3.5 py-2.5 bg-white text-ink focus:outline-none focus:ring-2 focus:ring-yellow
                              {{ $errors->has('nama_penjemput') ? 'border-red-500 bg-red-50' : 'border-ink' }}">
                @error('nama_penjemput')
                    <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hubungan --}}
            <div>
                <label for="hubungan" class="block text-sm font-semibold mb-1.5">Hubungan dengan Anak</label>
                <select id="hubungan" name="hubungan"
                        class="w-full border-[1.5px] rounded-ta px-3.5 py-2.5 bg-white text-ink focus:outline-none focus:ring-2 focus:ring-yellow
                               {{ $errors->has('hubungan') ? 'border-red-500 bg-red-50' : 'border-ink' }}">
                    <option value="">— Pilih hubungan —</option>
                    @foreach ([
                        'ayah' => 'Ayah', 'ibu' => 'Ibu',
                        'kakek' => 'Kakek', 'nenek' => 'Nenek',
                        'paman' => 'Paman', 'bibi' => 'Bibi',
                        'kakak' => 'Kakak', 'saudara' => 'Saudara',
                        'tetangga' => 'Tetangga', 'lainnya' => 'Lainnya',
                    ] as $value => $label)
                        <option value="{{ $value }}" @selected(old('hubungan') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('hubungan')
                    <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- NIK --}}
            <div>
                <label for="nik" class="block text-sm font-semibold mb-1.5">NIK (16 digit)</label>
                <input id="nik" type="text" name="nik"
                       value="{{ old('nik') }}"
                       inputmode="numeric" maxlength="16"
                       placeholder="3273xxxxxxxxxxxx"
                       class="w-full border-[1.5px] rounded-ta px-3.5 py-2.5 bg-white text-ink focus:outline-none focus:ring-2 focus:ring-yellow
                              {{ $errors->has('nik') ? 'border-red-500 bg-red-50' : 'border-ink' }}">
                @error('nik')
                    <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- No HP --}}
            <div>
                <label for="no_hp" class="block text-sm font-semibold mb-1.5">No. HP Penjemput</label>
                <input id="no_hp" type="tel" name="no_hp"
                       value="{{ old('no_hp') }}"
                       placeholder="08xxxxxxxxxx"
                       class="w-full border-[1.5px] rounded-ta px-3.5 py-2.5 bg-white text-ink focus:outline-none focus:ring-2 focus:ring-yellow
                              {{ $errors->has('no_hp') ? 'border-red-500 bg-red-50' : 'border-ink' }}">
                @error('no_hp')
                    <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ---------- Section 3: Detail Penjemputan ---------- --}}
        <div class="flex items-center gap-3 mb-4">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border-[1.5px] border-ink font-bold text-ink">3</span>
            <h2 class="text-lg font-bold text-ink">Detail Penjemputan</h2>
        </div>

        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="waktu_jemput" class="block text-sm font-semibold mb-1.5">Waktu Penjemputan</label>
                    <input id="waktu_jemput" type="datetime-local" name="waktu_jemput"
                           value="{{ old('waktu_jemput', now()->addHour()->format('Y-m-d\TH:i')) }}"
                           class="w-full border-[1.5px] rounded-ta px-3.5 py-2.5 bg-white text-ink focus:outline-none focus:ring-2 focus:ring-yellow
                                  {{ $errors->has('waktu_jemput') ? 'border-red-500 bg-red-50' : 'border-ink' }}">
                    @error('waktu_jemput')
                        <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="alasan" class="block text-sm font-semibold mb-1.5">Alasan / Keperluan</label>
                <textarea id="alasan" name="alasan" rows="3"
                          placeholder="Contoh: Saya harus dinas luar kota, anak akan dijemput oleh nenek."
                          class="w-full border-[1.5px] rounded-ta px-3.5 py-2.5 bg-white text-ink focus:outline-none focus:ring-2 focus:ring-yellow
                                 {{ $errors->has('alasan') ? 'border-red-500 bg-red-50' : 'border-ink' }}">{{ old('alasan') }}</textarea>
                @error('alasan')
                    <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ---------- Section 4: Upload Identitas ---------- --}}
        <div class="flex items-center gap-3 mb-4">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border-[1.5px] border-ink font-bold text-ink">4</span>
            <h2 class="text-lg font-bold text-ink">Upload Identitas</h2>
        </div>

        <div class="mb-8">
            <label for="foto_identitas" class="block text-sm font-semibold mb-1.5">Foto KTP / Identitas Penjemput</label>
            <input id="foto_identitas" type="file" name="foto_identitas"
                   accept="image/jpeg,image/png,image/webp"
                   class="block w-full text-sm text-muted bg-white border-[1.5px] rounded-ta overflow-hidden cursor-pointer
                          file:mr-4 file:py-2.5 file:px-4 file:border-0 file:border-r-[1.5px] file:border-ink file:bg-yellow file:text-ink file:font-semibold
                          file:cursor-pointer file:hover:bg-cream
                          {{ $errors->has('foto_identitas') ? 'border-red-500 bg-red-50' : 'border-ink' }}">
            <p class="text-xs text-muted mt-1">
                Format: JPG, PNG, atau WEBP. Maksimal 2 MB. Foto disimpan secara privat.
            </p>
            @error('foto_identitas')
                <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
            @enderror

            {{-- Preview sederhana --}}
            <img id="preview-foto" src="" alt="Pratinjau foto identitas"
                 class="hidden mt-3 max-h-48 rounded-ta border-[1.5px] border-ink">
        </div>

        {{-- ---------- Actions ---------- --}}
        <div class="flex flex-wrap gap-3 pt-4 border-t-[1.5px] border-ink">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 font-semibold text-sm px-5 py-3 bg-yellow border-[1.5px] border-ink rounded-ta text-ink hover:-translate-y-0.5 transition-transform">
                Kirim Pengajuan
            </button>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center justify-center gap-2 font-semibold text-sm px-5 py-3 bg-white border-[1.5px] border-ink rounded-ta text-ink hover:-translate-y-0.5 transition-transform">
                Batal
            </a>
        </div>
    </form>

    @endif
</div>

{{-- Script kecil untuk preview foto (opsional, tidak wajib) --}}
<script>
    (function () {
        var input = document.getElementById('foto_identitas');
        var preview = document.getElementById('preview-foto');
        if (!input || !preview) return;

        input.addEventListener('change', function (e) {
            var file = e.target.files && e.target.files[0];
            if (!file) {
                preview.src = '';
                preview.classList.add('hidden');
                return;
            }
            var url = URL.createObjectURL(file);
            preview.src = url;
            preview.classList.remove('hidden');
        });
    })();
</script>
@endsection