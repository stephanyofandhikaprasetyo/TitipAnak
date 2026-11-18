@extends('layouts.pengasuh')

@section('title', 'Ubah Laporan Harian & Kelola Foto')

@section('content')
    {{-- Header Halaman --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#A8D4FF] border border-[#1B1B1B]">
                    Pengasuh
                </span>
                <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-[#CDBDFF] border border-[#1B1B1B]">
                    Edit Laporan &amp; Dokumentasi
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                Laporan Harian: {{ $laporan->anak?->nama_tampil }}
            </h1>
            <p class="text-sm text-[#6B6B6B] mt-1">
                Perbarui informasi laporan harian yang kurang atau kelola foto kegiatan anak ({{ $laporan->tanggal?->translatedFormat('d M Y') }}).
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
                <div class="font-bold">Terdapat kesalahan pada input Anda:</div>
                <ul class="list-disc pl-5 mt-1 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- BAGIAN A (Stage 7): Form Ubah Data Laporan Harian --}}
    <form method="POST"
          action="{{ route('pengasuh.laporan-harian.update', $laporan->id) }}"
          class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between gap-3 border-b border-[#1B1B1B] pb-3">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    1
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Perbarui Catatan Laporan Harian</h2>
                    <p class="text-xs text-[#6B6B6B]">Lengkapi atau perbaiki data makan, tidur, BAB/BAK, aktivitas, dan kondisi anak.</p>
                </div>
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
                        <option value="{{ $anak->id }}" @selected((int) old('anak_id', $laporan->anak_id) === $anak->id)>
                            {{ $anak->nama_lengkap }} ({{ $anak->kode_anak }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="tanggal" class="block text-sm font-semibold">Tanggal Laporan <span class="text-[#B02E12]">*</span></label>
                <input id="tanggal"
                       type="date"
                       name="tanggal"
                       max="{{ now()->format('Y-m-d') }}"
                       value="{{ old('tanggal', $laporan->tanggal?->format('Y-m-d')) }}"
                       required
                       class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
            </div>

            <div class="space-y-1.5">
                <label for="kondisi" class="block text-sm font-semibold">Kondisi Umum Anak <span class="text-[#B02E12]">*</span></label>
                <select id="kondisi"
                        name="kondisi"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
                    @foreach ($daftarKondisi as $kode => $label)
                        <option value="{{ $kode }}" @selected(old('kondisi', $laporan->kondisi) === $kode)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="tidur" class="block text-sm font-semibold">Catatan Tidur / Istirahat <span class="text-[#B02E12]">*</span></label>
                <input id="tidur"
                       type="text"
                       name="tidur"
                       value="{{ old('tidur', $laporan->tidur) }}"
                       required
                       class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
            </div>

            <div class="space-y-1.5">
                <label for="bab_bak" class="block text-sm font-semibold">Catatan BAB &amp; BAK <span class="text-[#B02E12]">*</span></label>
                <input id="bab_bak"
                       type="text"
                       name="bab_bak"
                       value="{{ old('bab_bak', $laporan->bab_bak) }}"
                       required
                       class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">
            </div>

            <div class="space-y-1.5">
                <label for="makan" class="block text-sm font-semibold">Catatan Makan &amp; Minum <span class="text-[#B02E12]">*</span></label>
                <textarea id="makan"
                          name="makan"
                          rows="2"
                          required
                          class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">{{ old('makan', $laporan->makan) }}</textarea>
            </div>

            <div class="sm:col-span-2 space-y-1.5">
                <label for="aktivitas" class="block text-sm font-semibold">Aktivitas &amp; Perkembangan Hari Ini <span class="text-[#B02E12]">*</span></label>
                <textarea id="aktivitas"
                          name="aktivitas"
                          rows="3"
                          required
                          class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">{{ old('aktivitas', $laporan->aktivitas) }}</textarea>
            </div>

            <div class="sm:col-span-2 space-y-1.5">
                <label for="catatan_tambahan" class="block text-sm font-semibold">Catatan Tambahan untuk Orang Tua</label>
                <textarea id="catatan_tambahan"
                          name="catatan_tambahan"
                          rows="2"
                          class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white focus:outline-none focus:bg-[#FFF6D6]">{{ old('catatan_tambahan', $laporan->catatan_tambahan) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center justify-center font-semibold text-sm px-6 py-3 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                Simpan Perubahan Laporan
            </button>
        </div>
    </form>

    {{-- BAGIAN B (Stage 5 & Stage 6): Galeri Foto Kegiatan & Unggah Foto Baru --}}
    <section id="kelola-foto" class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-5 sm:p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-[#1B1B1B] pb-3">
            <div class="flex items-center gap-3">
                <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
                    2
                </span>
                <div>
                    <h2 class="text-lg font-bold leading-tight">Foto Kegiatan Laporan Harian</h2>
                    <p class="text-xs text-[#6B6B6B]">Unggah foto kegiatan baru, ganti foto yang salah unggah, atau hapus foto.</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-[#A8D4FF] border border-[#1B1B1B] self-start sm:self-auto">
                {{ $laporan->foto->count() }} Foto Terlampir
            </span>
        </div>

        {{-- Form Unggah Tambahan Foto Kegiatan (Stage 6) --}}
        <form method="POST"
              action="{{ route('pengasuh.laporan-harian.foto.store', $laporan->id) }}"
              enctype="multipart/form-data"
              class="bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] p-4 space-y-3">
            @csrf
            <div class="font-bold text-sm">+ Unggah Foto Kegiatan Baru</div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-6 space-y-1">
                    <label for="tambah_foto_kegiatan" class="block text-xs font-semibold">Pilih File Foto (Maks. 5 foto, 2 MB/foto)</label>
                    <input id="tambah_foto_kegiatan"
                           type="file"
                           name="foto_kegiatan[]"
                           multiple
                           required
                           accept="image/jpeg,image/png,image/webp"
                           class="w-full px-3 py-2 text-xs border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white file:mr-2.5 file:py-1 file:px-2.5 file:rounded-[8px] file:border file:border-[#1B1B1B] file:bg-[#FFD43B] file:font-semibold cursor-pointer">
                </div>

                <div class="md:col-span-4 space-y-1">
                    <label for="keterangan_foto_baru" class="block text-xs font-semibold">Keterangan Kegiatan (Opsional)</label>
                    <input id="keterangan_foto_baru"
                           type="text"
                           name="keterangan_foto"
                           placeholder="Contoh: Bermain balok di ruang kelas"
                           class="w-full px-3 py-2.5 text-xs border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white">
                </div>

                <div class="md:col-span-2">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center font-semibold text-xs px-4 py-2.5 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                        Unggah Foto
                    </button>
                </div>
            </div>
        </form>

        {{-- Daftar Foto yang Sudah Diunggah (Stage 5: Ubah / Hapus Foto) --}}
        @if ($laporan->foto->isEmpty())
            <div class="border border-dashed border-[#1B1B1B] rounded-[10px] p-6 text-center text-sm text-[#6B6B6B]">
                Belum ada foto kegiatan yang diunggah pada laporan harian ini.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($laporan->foto as $foto)
                    <div class="border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-white flex flex-col justify-between">
                        <div>
                            <div class="bg-[#FFF6D6] border-b-[1.5px] border-[#1B1B1B] aspect-[4/3] overflow-hidden">
                                <img src="{{ $foto->foto_url }}"
                                     alt="{{ $foto->keterangan ?? 'Foto kegiatan anak' }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="p-3.5">
                                <div class="text-xs font-semibold">
                                    {{ $foto->keterangan ?: 'Tanpa keterangan foto' }}
                                </div>
                                <div class="text-[11px] text-[#6B6B6B] mt-0.5">
                                    Diunggah {{ $foto->created_at?->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-[#FFF6D6] border-t border-[#1B1B1B] flex items-center justify-end gap-2">
                            <button type="button"
                                    data-edit-foto
                                    data-action="{{ route('pengasuh.laporan-harian.foto.update', [$laporan->id, $foto->id]) }}"
                                    data-url="{{ $foto->foto_url }}"
                                    data-keterangan="{{ $foto->keterangan ?? '' }}"
                                    class="inline-flex items-center font-semibold text-xs px-3 py-1.5 rounded-[10px] bg-[#A8D4FF] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                Ubah / Ganti Foto
                            </button>

                            <button type="button"
                                    data-delete-foto
                                    data-action="{{ route('pengasuh.laporan-harian.foto.destroy', [$laporan->id, $foto->id]) }}"
                                    data-keterangan="{{ $foto->keterangan ?: 'Foto kegiatan #' . $loop->iteration }}"
                                    class="inline-flex items-center font-semibold text-xs px-3 py-1.5 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Modal Ubah / Ganti Foto Kegiatan (Stage 5) --}}
    <dialog id="modal-edit-foto" class="ta-modal p-0 border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(500px,calc(100vw-32px))] overflow-hidden">
        <form id="form-edit-foto" method="POST" action="" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FFD43B] border-b-[1.5px] border-[#1B1B1B]">
                <h3 class="text-lg font-bold m-0">Ubah / Ganti Foto Kegiatan</h3>
                <button type="button" data-close-modal="modal-edit-foto" aria-label="Tutup"
                        class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="p-5 space-y-4 text-sm">
                <div class="border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden bg-[#FFF6D6] p-2 text-center">
                    <img id="ef-preview" src="" alt="Foto saat ini" class="max-h-48 mx-auto rounded-[8px] object-contain">
                </div>

                <div class="space-y-1.5">
                    <label for="ef-file" class="block text-xs font-bold">Ganti dengan File Foto Baru (Opsional)</label>
                    <input id="ef-file"
                           type="file"
                           name="foto_baru"
                           accept="image/jpeg,image/png,image/webp"
                           class="w-full px-3 py-2 text-xs border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white file:mr-2.5 file:py-1 file:px-2.5 file:rounded-[8px] file:border file:border-[#1B1B1B] file:bg-[#FFD43B] file:font-semibold cursor-pointer">
                    <span class="block text-[11px] text-[#6B6B6B]">Biarkan kosong jika hanya ingin mengubah keterangan foto.</span>
                </div>

                <div class="space-y-1.5">
                    <label for="ef-keterangan" class="block text-xs font-bold">Keterangan Foto</label>
                    <input id="ef-keterangan"
                           type="text"
                           name="keterangan"
                           placeholder="Masukkan keterangan kegiatan..."
                           class="w-full px-3.5 py-2.5 text-sm border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white">
                </div>
            </div>

            <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                <button type="button" data-close-modal="modal-edit-foto"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

    {{-- Modal Konfirmasi Hapus Foto Kegiatan (Stage 5 — Header Coral) --}}
    <dialog id="modal-hapus-foto" class="ta-modal p-0 border-[1.5px] border-[#1B1B1B] rounded-[10px] bg-white text-[#1B1B1B] w-[min(460px,calc(100vw-32px))] overflow-hidden">
        <form id="form-hapus-foto" method="POST" action="">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#FF9E80] border-b-[1.5px] border-[#1B1B1B]">
                <h3 class="text-lg font-bold m-0">Hapus Foto Kegiatan?</h3>
                <button type="button" data-close-modal="modal-hapus-foto" aria-label="Tutup"
                        class="w-9 h-9 grid place-items-center rounded-full bg-white border-[1.5px] border-[#1B1B1B] font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="p-5 space-y-2 text-sm">
                <p class="m-0">
                    Anda yakin ingin menghapus foto <b id="hf-keterangan">ini</b> yang salah unggah?
                </p>
                <p class="text-xs text-[#6B6B6B] m-0">
                    File foto akan dihapus permanen dari penyimpanan, sedangkan catatan laporan harian tetap tersimpan.
                </p>
            </div>

            <div class="flex justify-end gap-3 px-5 py-3.5 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                <button type="button" data-close-modal="modal-hapus-foto"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="font-semibold text-sm px-4 py-2 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] cursor-pointer">
                    Ya, Hapus Foto
                </button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editFotoModal  = document.getElementById('modal-edit-foto');
    const hapusFotoModal = document.getElementById('modal-hapus-foto');
    const efFile         = document.getElementById('ef-file');
    const efPreview      = document.getElementById('ef-preview');

    document.querySelectorAll('[data-edit-foto]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('form-edit-foto').action     = btn.dataset.action;
            document.getElementById('ef-keterangan').value       = btn.dataset.keterangan || '';
            if (efPreview) efPreview.src = btn.dataset.url || '';
            if (efFile) efFile.value = '';

            if (editFotoModal && editFotoModal.showModal) editFotoModal.showModal();
        });
    });

    if (efFile && efPreview) {
        efFile.addEventListener('change', function () {
            const file = efFile.files && efFile.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) { efPreview.src = e.target.result; };
            reader.readAsDataURL(file);
        });
    }

    document.querySelectorAll('[data-delete-foto]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('form-hapus-foto').action = btn.dataset.action;
            document.getElementById('hf-keterangan').textContent = btn.dataset.keterangan || 'ini';

            if (hapusFotoModal && hapusFotoModal.showModal) hapusFotoModal.showModal();
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(btn.dataset.closeModal);
            if (target) target.close();
        });
    });

    [editFotoModal, hapusFotoModal].forEach(function (dialog) {
        if (!dialog) return;
        dialog.addEventListener('click', function (e) {
            if (e.target === dialog) dialog.close();
        });
    });
});
</script>
@endpush