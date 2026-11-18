@extends('layouts.pengasuh')

@section('title', 'Ubah Laporan Insiden')

@section('content')
    {{-- Header Halaman --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#A8D4FF] border border-[#1B1B1B]">
                    Pengasuh
                </span>
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#FFD43B] border border-[#1B1B1B]">
                    Pembaruan Informasi Insiden
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Ubah Laporan Insiden</h1>
            <p class="text-sm text-[#6B6B6B] mt-1">
                Tambahkan perkembangan kondisi terbaru, perbarui tindakan pertolongan, atau ubah status penanganan insiden.
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
                <div class="font-bold">Periksa kembali pembaruan laporan insiden:</div>
                <ul class="list-disc pl-5 mt-1 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ route('pengasuh.laporan-insiden.update', $insiden->id) }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Bagian 1: Data Kejadian & Status Penanganan --}}
        <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    1
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Data Kejadian &amp; Status Penanganan</h2>
                    <p class="text-xs text-[#6B6B6B]">Perbarui status penanganan apabila kondisi anak sudah membaik atau selesai dipantau.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="anak_id" class="block text-sm font-semibold">Nama Anak <span class="text-[#B02E12]">*</span></label>
                    <select id="anak_id"
                            name="anak_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                        @foreach ($daftarAnak as $anak)
                            <option value="{{ $anak->id }}" @selected((int) old('anak_id', $insiden->anak_id) === $anak->id)>
                                {{ $anak->nama_lengkap }} ({{ $anak->kode_anak }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="waktu_kejadian" class="block text-sm font-semibold">Waktu Kejadian <span class="text-[#B02E12]">*</span></label>
                    <input id="waktu_kejadian"
                           type="datetime-local"
                           name="waktu_kejadian"
                           max="{{ now()->format('Y-m-d\TH:i') }}"
                           value="{{ old('waktu_kejadian', $insiden->waktu_kejadian?->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label for="judul_insiden" class="block text-sm font-semibold">Judul Singkat Insiden <span class="text-[#B02E12]">*</span></label>
                    <input id="judul_insiden"
                           type="text"
                           name="judul_insiden"
                           value="{{ old('judul_insiden', $insiden->judul_insiden) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                </div>

                <div class="space-y-1.5">
                    <label for="tingkat_keparahan" class="block text-sm font-semibold">Tingkat Keparahan <span class="text-[#B02E12]">*</span></label>
                    <select id="tingkat_keparahan"
                            name="tingkat_keparahan"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                        @foreach ($daftarKeparahan as $kode => $label)
                            <option value="{{ $kode }}" @selected(old('tingkat_keparahan', $insiden->tingkat_keparahan) === $kode)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="status_penanganan" class="block text-sm font-semibold">Status Penanganan <span class="text-[#B02E12]">*</span></label>
                    <select id="status_penanganan"
                            name="status_penanganan"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                        @foreach ($daftarStatus as $kode => $label)
                            <option value="{{ $kode }}" @selected(old('status_penanganan', $insiden->status_penanganan) === $kode)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- Bagian 2: Kronologi Tambahan, Tindakan & Foto Bukti --}}
        <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    2
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Kronologi, Tindakan Lanjutan &amp; Foto Bukti</h2>
                    <p class="text-xs text-[#6B6B6B]">Tambahkan informasi detail atau hasil pemantauan lanjutan pada bagian ini.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="kronologi" class="block text-sm font-semibold">Kronologi Kejadian &amp; Informasi Tambahan <span class="text-[#B02E12]">*</span></label>
                    <textarea id="kronologi"
                              name="kronologi"
                              rows="4"
                              required
                              class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">{{ old('kronologi', $insiden->kronologi) }}</textarea>
                </div>

                <div class="space-y-1.5">
                    <label for="tindakan_pertolongan" class="block text-sm font-semibold">Tindakan Pertolongan &amp; Pemantauan Lanjutan <span class="text-[#B02E12]">*</span></label>
                    <textarea id="tindakan_pertolongan"
                              name="tindakan_pertolongan"
                              rows="3"
                              required
                              class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">{{ old('tindakan_pertolongan', $insiden->tindakan_pertolongan) }}</textarea>
                </div>

                @if ($insiden->foto_bukti_url)
                    <div class="p-4 rounded-[10px] bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] space-y-3">
                        <div class="text-xs font-bold">Foto Bukti Saat Ini</div>
                        <img src="{{ $insiden->foto_bukti_url }}" alt="Foto bukti insiden saat ini" class="max-h-48 rounded-[8px] border border-[#1B1B1B] bg-white">
                        <label class="inline-flex items-center gap-2 text-xs font-semibold cursor-pointer">
                            <input type="checkbox" name="hapus_foto_bukti" value="1" class="w-4 h-4 accent-[#1B1B1B]">
                            <span>Hapus foto bukti saat ini</span>
                        </label>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label for="foto_bukti" class="block text-sm font-semibold">
                        {{ $insiden->foto_bukti_url ? 'Ganti dengan Foto Bukti Baru (Opsional)' : 'Unggah Foto Bukti Insiden (Opsional)' }}
                    </label>
                    <input id="foto_bukti"
                           type="file"
                           name="foto_bukti"
                           accept="image/jpeg,image/png,image/webp"
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-[8px] file:border file:border-[#1B1B1B] file:bg-[#FFD43B] file:font-semibold file:text-xs cursor-pointer">
                    <span class="block text-xs text-[#6B6B6B]">Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.</span>
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
                Simpan Pembaruan Insiden
            </button>
        </div>
    </form>
@endsection