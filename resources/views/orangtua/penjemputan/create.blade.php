@extends('layouts.orangtua')

@section('title', 'Ajukan Penjemputan Baru')

@section('content')
    {{-- Header Halaman --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#A8E6CF] border border-[#1B1B1B]">
                    Orang Tua
                </span>
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#FFD43B] border border-[#1B1B1B]">
                    Formulir Penjemputan
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Ajukan Penjemputan Baru</h1>
            <p class="text-sm text-[#6B6B6B] mt-1">
                Lengkapi data jadwal dan identitas wali yang akan menjemput anak Anda di daycare.
            </p>
        </div>

        <a href="{{ route('orangtua.penjemputan.index') }}"
           class="inline-flex items-center justify-center font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
            ← Kembali ke Riwayat
        </a>
    </header>

    {{-- Alert Validasi Global --}}
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

    @if ($daftarAnak->isEmpty())
        <div class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-6 text-center space-y-2">
            <span class="inline-grid place-items-center w-14 h-14 rounded-full bg-white border-[1.5px] border-[#1B1B1B] text-2xl">
                🧸
            </span>
            <h2 class="text-lg font-bold">Tidak ada anak yang memenuhi syarat penjemputan</h2>
            <p class="text-sm text-[#6B6B6B] max-w-md mx-auto">
                Pastikan data anak Anda berstatus aktif dan status kehadirannya adalah Belum Datang atau Hadir di daycare.
            </p>
        </div>
    @else
        @php
            $oldAnakId       = (int) old('anak_id', $selectedAnakId);
            $oldMode         = old('mode_penjemput', 'baru');
            $oldPenjemputId  = (int) old('penjemput_id');
            $defaultWaktu    = old('waktu_jemput', now()->addMinutes(30)->format('Y-m-d\TH:i'));
        @endphp

        <form method="POST"
              action="{{ route('orangtua.penjemputan.store') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf

            {{-- Langkah 1: Pilih Anak --}}
            <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                        1
                    </span>
                    <div>
                        <h2 class="text-lg font-bold leading-tight">Pilih Anak yang Akan Dijemput</h2>
                        <p class="text-xs text-[#6B6B6B]">Pilih salah satu anak Anda yang terdaftar aktif.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($daftarAnak as $anak)
                        @php
                            $sedangDiajukan = in_array($anak->id, $anakSedangDiajukanIds, true);
                        @endphp
                        <label class="block cursor-pointer">
                            <input type="radio"
                                   name="anak_id"
                                   value="{{ $anak->id }}"
                                   class="peer sr-only anak-radio"
                                   data-penjemput='@json($anak->penjemput->map(fn ($p) => [
                                       "id" => $p->id,
                                       "nama" => $p->nama,
                                       "hubungan" => ucfirst($p->hubungan),
                                       "no_hp" => $p->no_hp,
                                       "nik_samar" => $p->nik_samar,
                                   ])->values())'
                                   @checked($oldAnakId === $anak->id)
                                   required>

                            <div class="h-full p-4 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white peer-checked:bg-[#A8E6CF] transition-colors space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-bold text-base">{{ $anak->nama_lengkap }}</div>
                                        <div class="text-xs text-[#6B6B6B]">
                                            {{ $anak->kode_anak }} · {{ $anak->kelas?->nama_kelas ?? 'Tanpa Kelas' }}
                                        </div>
                                    </div>
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-white border border-[#1B1B1B]">
                                        {{ $anak->status_kehadiran_label }}
                                    </span>
                                </div>

                                @if ($anak->catatan_medis)
                                    <div class="text-xs bg-white/80 border border-[#1B1B1B] rounded-[8px] px-2.5 py-1.5">
                                        <b>Catatan Medis:</b> {{ $anak->catatan_medis }}
                                    </div>
                                @endif

                                @if ($sedangDiajukan)
                                    <div class="text-xs font-semibold px-2.5 py-1 rounded-[8px] bg-[#FF9E80] border border-[#1B1B1B]">
                                        ⚠️ Sudah memiliki pengajuan penjemputan aktif
                                    </div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('anak_id')
                    <p class="text-xs font-semibold text-[#B02E12]">{{ $message }}</p>
                @enderror
            </section>

            {{-- Langkah 2: Data Wali Penjemput --}}
            <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#A8D4FF] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                        2
                    </span>
                    <div>
                        <h2 class="text-lg font-bold leading-tight">Identitas Wali Penjemput</h2>
                        <p class="text-xs text-[#6B6B6B]">Pilih dari daftar penjemput yang pernah didaftarkan atau daftarkan penjemput baru.</p>
                    </div>
                </div>

                {{-- Toggle Mode Penjemput --}}
                <div class="flex flex-wrap gap-3" role="radiogroup" aria-label="Tipe Penjemput">
                    <label class="cursor-pointer">
                        <input type="radio" name="mode_penjemput" value="baru" class="peer sr-only mode-radio" @checked($oldMode === 'baru')>
                        <span class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-full border-[1.5px] border-[#1B1B1B] bg-white peer-checked:bg-[#1B1B1B] peer-checked:text-white transition-colors">
                            <span>➕</span>
                            <span>Input Penjemput Baru</span>
                        </span>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="mode_penjemput" value="terdaftar" class="peer sr-only mode-radio" @checked($oldMode === 'terdaftar')>
                        <span class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-full border-[1.5px] border-[#1B1B1B] bg-white peer-checked:bg-[#1B1B1B] peer-checked:text-white transition-colors">
                            <span>📋</span>
                            <span>Pilih Penjemput Terdaftar</span>
                        </span>
                    </label>
                </div>

                {{-- Panel Mode: Pilih Penjemput Terdaftar --}}
                <div id="panel-terdaftar" class="space-y-3 {{ $oldMode === 'terdaftar' ? '' : 'hidden' }}">
                    <div class="space-y-1.5">
                        <label for="penjemput_id" class="block text-sm font-semibold">Pilih Wali Penjemput Terdaftar</label>
                        <select id="penjemput_id"
                                name="penjemput_id"
                                data-old="{{ $oldPenjemputId }}"
                                class="w-full px-3.5 py-2.5 text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6]">
                            <option value="">-- Pilih wali penjemput --</option>
                        </select>
                        <p id="hint-penjemput-kosong" class="text-xs text-[#6B6B6B] hidden">
                            Anak ini belum memiliki wali penjemput terdaftar. Silakan pilih opsi <b>Input Penjemput Baru</b>.
                        </p>
                        @error('penjemput_id')
                            <p class="text-xs font-semibold text-[#B02E12]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Panel Mode: Input Penjemput Baru --}}
                <div id="panel-baru" class="grid grid-cols-1 sm:grid-cols-2 gap-4 {{ $oldMode === 'baru' ? '' : 'hidden' }}">
                    <div class="space-y-1.5">
                        <label for="nama" class="block text-sm font-semibold">Nama Lengkap Penjemput <span class="text-[#B02E12]">*</span></label>
                        <input id="nama"
                               type="text"
                               name="nama"
                               value="{{ old('nama') }}"
                               placeholder="Contoh: Siti Rahmawati"
                               class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('nama') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                        <span class="block text-xs text-[#6B6B6B]">Harus sesuai nama pada KTP/identitas resmi.</span>
                        @error('nama')
                            <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="hubungan" class="block text-sm font-semibold">Hubungan dengan Anak <span class="text-[#B02E12]">*</span></label>
                        <input id="hubungan"
                               type="text"
                               name="hubungan"
                               value="{{ old('hubungan') }}"
                               list="daftar-hubungan"
                               placeholder="Contoh: Ibu, Ayah, Bibi, Nenek, Paman"
                               class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('hubungan') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                        <datalist id="daftar-hubungan">
                            <option value="Ayah">
                            <option value="Ibu">
                            <option value="Nenek">
                            <option value="Kakek">
                            <option value="Bibi">
                            <option value="Paman">
                            <option value="Kakak">
                        </datalist>
                        @error('hubungan')
                            <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="nik" class="block text-sm font-semibold">Nomor Induk Kependudukan (NIK) <span class="text-[#B02E12]">*</span></label>
                        <input id="nik"
                               type="text"
                               name="nik"
                               inputmode="numeric"
                               maxlength="16"
                               value="{{ old('nik') }}"
                               placeholder="16 digit angka NIK KTP"
                               class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('nik') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                        <span class="block text-xs text-[#6B6B6B]">Dicocokkan pengasuh saat serah terima anak.</span>
                        @error('nik')
                            <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="no_hp" class="block text-sm font-semibold">Nomor HP / WhatsApp Aktif <span class="text-[#B02E12]">*</span></label>
                        <input id="no_hp"
                               type="tel"
                               name="no_hp"
                               value="{{ old('no_hp') }}"
                               placeholder="Contoh: 081234567890"
                               class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('no_hp') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">
                        @error('no_hp')
                            <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Langkah 3: Jadwal, Metode Verifikasi & Bukti Foto --}}
            <section class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#CDBDFF] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                        3
                    </span>
                    <div>
                        <h2 class="text-lg font-bold leading-tight">Jadwal &amp; Verifikasi Penjemputan</h2>
                        <p class="text-xs text-[#6B6B6B]">Tentukan waktu perkiraan tiba dan unggah foto identitas/KTP penjemput.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="waktu_jemput" class="block text-sm font-semibold">Perkiraan Waktu Penjemputan <span class="text-[#B02E12]">*</span></label>
                        <input id="waktu_jemput"
                               type="datetime-local"
                               name="waktu_jemput"
                               value="{{ $defaultWaktu }}"
                               class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('waktu_jemput') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}"
                               required>
                        @error('waktu_jemput')
                            <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="metode_verifikasi" class="block text-sm font-semibold">Metode Verifikasi <span class="text-[#B02E12]">*</span></label>
                        <select id="metode_verifikasi"
                                name="metode_verifikasi"
                                class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('metode_verifikasi') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}"
                                required>
                            @foreach ($daftarMetode as $kodeMetode => $labelMetode)
                                <option value="{{ $kodeMetode }}" @selected(old('metode_verifikasi', 'ktp') === $kodeMetode)>
                                    {{ $labelMetode }}
                                </option>
                            @endforeach
                        </select>
                        @error('metode_verifikasi')
                            <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="foto_bukti" class="block text-sm font-semibold">
                            Foto Identitas / KTP Penjemput
                            <span id="label-foto-wajib" class="text-[#B02E12]">*</span>
                            <span id="label-foto-opsional" class="text-xs font-normal text-[#6B6B6B] hidden">(Opsional jika memakai foto identitas terdaftar)</span>
                        </label>
                        <input id="foto_bukti"
                               type="file"
                               name="foto_bukti"
                               accept="image/jpeg,image/png,image/webp"
                               class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-[8px] file:border file:border-[#1B1B1B] file:bg-[#FFD43B] file:font-semibold file:text-xs cursor-pointer {{ $errors->has('foto_bukti') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B]' }}">
                        <span class="block text-xs text-[#6B6B6B]">Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.</span>
                        @error('foto_bukti')
                            <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                        @enderror

                        <div id="preview-foto-wrap" class="hidden pt-2">
                            <div class="inline-block border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6] p-2">
                                <img id="preview-foto-img" src="" alt="Pratinjau foto identitas" class="max-h-44 rounded-[8px] object-contain">
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="catatan" class="block text-sm font-semibold">Catatan untuk Pengasuh</label>
                        <textarea id="catatan"
                                  name="catatan"
                                  rows="3"
                                  placeholder="Contoh: Anak agak masuk angin, mohon siapkan jaket sebelum dijemput."
                                  class="w-full px-3.5 py-2.5 text-sm border-[1.5px] rounded-[10px] focus:outline-none focus:bg-[#FFF6D6] {{ $errors->has('catatan') ? 'border-[#C2371A] bg-[#FFF1EC]' : 'border-[#1B1B1B] bg-white' }}">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <span class="block text-xs font-semibold text-[#B02E12]">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Tombol Submit --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('orangtua.penjemputan.index') }}"
                   class="inline-flex items-center justify-center font-semibold text-sm px-5 py-3 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform no-underline text-[#1B1B1B]">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center font-semibold text-sm px-6 py-3 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                    Kirim Pengajuan Penjemputan
                </button>
            </div>
        </form>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const anakRadios = document.querySelectorAll('.anak-radio');
    const modeRadios = document.querySelectorAll('.mode-radio');
    const panelTerdaftar = document.getElementById('panel-terdaftar');
    const panelBaru = document.getElementById('panel-baru');
    const selectPenjemput = document.getElementById('penjemput_id');
    const hintKosong = document.getElementById('hint-penjemput-kosong');
    const labelWajib = document.getElementById('label-foto-wajib');
    const labelOpsional = document.getElementById('label-foto-opsional');
    const inputFoto = document.getElementById('foto_bukti');
    const previewWrap = document.getElementById('preview-foto-wrap');
    const previewImg = document.getElementById('preview-foto-img');

    function populatePenjemput() {
        const checkedAnak = document.querySelector('.anak-radio:checked');
        if (!checkedAnak || !selectPenjemput) return;

        const daftar = JSON.parse(checkedAnak.dataset.penjemput || '[]');
        const oldVal = selectPenjemput.dataset.old || '';

        selectPenjemput.innerHTML = '<option value="">-- Pilih wali penjemput --</option>';

        daftar.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nama + ' (' + item.hubungan + ' · ' + item.no_hp + ')';
            if (String(item.id) === String(oldVal)) {
                opt.selected = true;
            }
            selectPenjemput.appendChild(opt);
        });

        if (hintKosong) {
            hintKosong.classList.toggle('hidden', daftar.length > 0);
        }
    }

    function syncMode() {
        const checkedMode = document.querySelector('.mode-radio:checked');
        const mode = checkedMode ? checkedMode.value : 'baru';

        if (panelTerdaftar && panelBaru) {
            panelTerdaftar.classList.toggle('hidden', mode !== 'terdaftar');
            panelBaru.classList.toggle('hidden', mode !== 'baru');
        }

        if (labelWajib && labelOpsional) {
            labelWajib.classList.toggle('hidden', mode !== 'baru');
            labelOpsional.classList.toggle('hidden', mode === 'baru');
        }
    }

    anakRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (selectPenjemput) selectPenjemput.dataset.old = '';
            populatePenjemput();
        });
    });

    modeRadios.forEach(function (radio) {
        radio.addEventListener('change', syncMode);
    });

    if (inputFoto && previewWrap && previewImg) {
        inputFoto.addEventListener('change', function () {
            const file = inputFoto.files && inputFoto.files[0];
            if (!file) {
                previewWrap.classList.add('hidden');
                previewImg.src = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }

    populatePenjemput();
    syncMode();
});
</script>
@endpush