@extends('layouts.pengasuh')

@section('title', 'Catat Laporan Insiden')

@section('content')
    {{-- Header Halaman --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#A8D4FF] border border-[#1B1B1B]">
                    Pengasuh
                </span>
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#FF9E80] border border-[#1B1B1B]">
                    Pencatatan Insiden Baru
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Catat Laporan Insiden Anak</h1>
            <p class="text-sm text-[#6B6B6B] mt-1">
                Dokumentasikan kejadian insiden secara objektif beserta langkah pertolongan pertama yang telah diberikan.
            </p>
        </div>

        <a href="{{ route('pengasuh.laporan-insiden.index') }}"
           class="inline-flex items-center justify-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
            ← Kembali ke Daftar Insiden
        </a>
    </header>

    {{-- Alert Validasi Global --}}
    @if ($errors->any())
        <div role="alert" class="flex items-start gap-3 p-4 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] text-sm">
            <span class="text-base leading-none">⚠️</span>
            <div>
                <div class="font-bold">Periksa kembali isian laporan insiden:</div>
                <ul class="list-disc pl-5 mt-1 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ route('pengasuh.laporan-insiden.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf

        {{-- Bagian 1: Data Kejadian & Anak --}}
        <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    1
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Identitas Anak &amp; Waktu Kejadian</h2>
                    <p class="text-xs text-[#6B6B6B]">Pilih anak yang mengalami insiden dan kapan peristiwa terjadi.</p>
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
                            <option value="{{ $anak->id }}" @selected((int) old('anak_id', $selectedAnakId) === $anak->id)>
                                {{ $anak->nama_lengkap }} ({{ $anak->kode_anak }})
                            </option>
                        @endforeach
                    </select>
                    @error('anak_id')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="waktu_kejadian" class="block text-sm font-semibold">Waktu Kejadian <span class="text-[#B02E12]">*</span></label>
                    <input id="waktu_kejadian"
                           type="datetime-local"
                           name="waktu_kejadian"
                           max="{{ now()->format('Y-m-d\TH:i') }}"
                           value="{{ old('waktu_kejadian', now()->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('waktu_kejadian') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                    @error('waktu_kejadian')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label for="judul_insiden" class="block text-sm font-semibold">Judul Singkat Insiden <span class="text-[#B02E12]">*</span></label>
                    <input id="judul_insiden"
                           type="text"
                           name="judul_insiden"
                           value="{{ old('judul_insiden') }}"
                           placeholder="Contoh: Terjatuh ringan saat berlari di area bermain indoor"
                           required
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('judul_insiden') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                    @error('judul_insiden')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="tingkat_keparahan" class="block text-sm font-semibold">Tingkat Keparahan <span class="text-[#B02E12]">*</span></label>
                    <select id="tingkat_keparahan"
                            name="tingkat_keparahan"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('tingkat_keparahan') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                        @foreach ($daftarKeparahan as $kode => $label)
                            <option value="{{ $kode }}" @selected(old('tingkat_keparahan', 'ringan') === $kode)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('tingkat_keparahan')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="status_penanganan" class="block text-sm font-semibold">Status Penanganan Saat Ini <span class="text-[#B02E12]">*</span></label>
                    <select id="status_penanganan"
                            name="status_penanganan"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('status_penanganan') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                        @foreach ($daftarStatus as $kode => $label)
                            <option value="{{ $kode }}" @selected(old('status_penanganan', 'ditangani') === $kode)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status_penanganan')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Bagian 2: Kronologi, Tindakan Pertolongan & Foto Bukti --}}
        <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    2
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Kronologi &amp; Tindakan Pertolongan</h2>
                    <p class="text-xs text-[#6B6B6B]">Jelaskan penyebab kejadian, bagian tubuh yang terdampak, dan penanganan yang dilakukan.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="kronologi" class="block text-sm font-semibold">Kronologi Kejadian <span class="text-[#B02E12]">*</span></label>
                    <textarea id="kronologi"
                              name="kronologi"
                              rows="4"
                              placeholder="Contoh: Pukul 10.15 WIB saat bermain bola di matras busa, anak terpeleset sehingga lutut kiri terbentur ringan dan timbul kemerahan kecil."
                              required
                              class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('kronologi') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">{{ old('kronologi') }}</textarea>
                    @error('kronologi')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="tindakan_pertolongan" class="block text-sm font-semibold">Tindakan Pertolongan Pertama <span class="text-[#B02E12]">*</span></label>
                    <textarea id="tindakan_pertolongan"
                              name="tindakan_pertolongan"
                              rows="3"
                              placeholder="Contoh: Anak langsung ditenangkan, area lutut dibersihkan dengan air bersih dan dikompres dingin selama 10 menit. Anak kembali ceria dan dapat berjalan normal."
                              required
                              class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('tindakan_pertolongan') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">{{ old('tindakan_pertolongan') }}</textarea>
                    @error('tindakan_pertolongan')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="foto_bukti" class="block text-sm font-semibold">Foto Dokumentasi / Bukti Insiden (Opsional)</label>
                    <input id="foto_bukti"
                           type="file"
                           name="foto_bukti"
                           accept="image/jpeg,image/png,image/webp"
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-[8px] file:border file:border-[#1B1B1B] file:bg-[#FFD43B] file:font-semibold file:text-xs cursor-pointer">
                    <span class="block text-xs text-[#6B6B6B]">Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.</span>
                    @error('foto_bukti')
                        <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                    @enderror

                    <div id="preview-insiden-wrap" class="hidden pt-2">
                        <div class="inline-block border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6] p-2">
                            <img id="preview-insiden-img" src="" alt="Pratinjau foto insiden" class="max-h-44 rounded-[8px] object-contain">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Tombol Submit --}}
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('pengasuh.laporan-insiden.index') }}"
               class="inline-flex items-center justify-center font-semibold text-sm px-5 py-3 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center font-semibold text-sm px-6 py-3 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                Simpan Laporan Insiden
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputFoto = document.getElementById('foto_bukti');
    const wrap      = document.getElementById('preview-insiden-wrap');
    const img       = document.getElementById('preview-insiden-img');

    if (inputFoto && wrap && img) {
        inputFoto.addEventListener('change', function () {
            const file = inputFoto.files && inputFoto.files[0];
            if (!file) {
                wrap.classList.add('hidden');
                img.src = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                wrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endpush