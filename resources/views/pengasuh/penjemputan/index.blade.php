@extends('layouts.app')

@section('title', 'Validasi Penjemputan Anak - Pengasuh TitipAnak')

@section('content')
<div class="max-w-[1120px] mx-auto p-4 sm:p-6 space-y-6">

    {{-- Header Halaman Pengasuh (Aksen Blue #A8D4FF sesuai TitipAnak Design System) --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-[1.5px] border-[#1B1B1B] pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full border border-[#1B1B1B] bg-[#A8D4FF] text-[#1B1B1B]">
                    🧸 Pengasuh: {{ auth()->user()->name ?? 'Pengasuh' }}
                </span>
                <span class="text-xs text-[#6B6B6B]">Operasional Penyerahan Anak</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#1B1B1B]">Izin Penjemputan Masuk</h1>
        </div>

        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-white text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] hover:bg-[#FF9E80] transition-colors cursor-pointer">
                    🚪 Keluar
                </button>
            </form>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-[10px] bg-[#A8E6CF] border-[1.5px] border-[#1B1B1B] text-sm font-medium">
            <span class="text-xl">✅</span>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-center gap-3 p-4 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] text-sm font-medium">
            <span class="text-xl">⚠️</span>
            <div class="flex-1">{{ $errors->first() }}</div>
        </div>
    @endif

    {{-- Kartu Statistik Ringkas --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-[#FFD43B] text-center">
            <div class="text-4xl font-bold text-[#1B1B1B]">{{ $stats['pending'] }}</div>
            <div class="text-sm font-bold text-[#1B1B1B] mt-1">⏳ Menunggu Validasi</div>
        </div>
        <div class="p-5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-[#A8E6CF] text-center">
            <div class="text-4xl font-bold text-[#1B1B1B]">{{ $stats['disetujui'] }}</div>
            <div class="text-sm font-bold text-[#1B1B1B] mt-1">✅ Disetujui (Siap Jemput)</div>
        </div>
        <div class="p-5 rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-[#A8D4FF] text-center">
            <div class="text-4xl font-bold text-[#1B1B1B]">{{ $stats['selesai'] }}</div>
            <div class="text-sm font-bold text-[#1B1B1B] mt-1">🎉 Anak Sudah Diserahkan</div>
        </div>
    </div>

    {{-- Filter Tabs Status --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('pengasuh.penjemputan.index') }}"
           class="px-4 py-2 text-xs font-semibold rounded-full border-[1.5px] border-[#1B1B1B] {{ !$statusFilter ? 'bg-[#1B1B1B] text-white' : 'bg-white text-[#1B1B1B] hover:bg-[#FFF6D6]' }}">
            Semua Pengajuan
        </a>
        <a href="{{ route('pengasuh.penjemputan.index', ['status' => 'pending']) }}"
           class="px-4 py-2 text-xs font-semibold rounded-full border-[1.5px] border-[#1B1B1B] {{ $statusFilter === 'pending' ? 'bg-[#1B1B1B] text-white' : 'bg-white text-[#1B1B1B] hover:bg-[#FFF6D6]' }}">
            ⏳ Menunggu Verifikasi
        </a>
        <a href="{{ route('pengasuh.penjemputan.index', ['status' => 'disetujui']) }}"
           class="px-4 py-2 text-xs font-semibold rounded-full border-[1.5px] border-[#1B1B1B] {{ $statusFilter === 'disetujui' ? 'bg-[#1B1B1B] text-white' : 'bg-white text-[#1B1B1B] hover:bg-[#FFF6D6]' }}">
            ✅ Disetujui
        </a>
        <a href="{{ route('pengasuh.penjemputan.index', ['status' => 'selesai']) }}"
           class="px-4 py-2 text-xs font-semibold rounded-full border-[1.5px] border-[#1B1B1B] {{ $statusFilter === 'selesai' ? 'bg-[#1B1B1B] text-white' : 'bg-white text-[#1B1B1B] hover:bg-[#FFF6D6]' }}">
            🎉 Selesai
        </a>
        <a href="{{ route('pengasuh.penjemputan.index', ['status' => 'ditolak']) }}"
           class="px-4 py-2 text-xs font-semibold rounded-full border-[1.5px] border-[#1B1B1B] {{ $statusFilter === 'ditolak' ? 'bg-[#1B1B1B] text-white' : 'bg-white text-[#1B1B1B] hover:bg-[#FFF6D6]' }}">
            ❌ Ditolak
        </a>
    </div>

    {{-- Tabel Izin Penjemputan Masuk --}}
    @if($penjemputanList->isEmpty())
        <div class="p-12 text-center rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-[#FFF6D6]">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-white border-[1.5px] border-[#1B1B1B] grid place-items-center text-2xl">
                🧸
            </div>
            <h3 class="text-lg font-bold text-[#1B1B1B]">Tidak Ada Izin Penjemputan</h3>
            <p class="text-sm text-[#6B6B6B] mt-1">Belum ada data izin penjemputan pada kategori ini.</p>
        </div>
    @else
        <div class="rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#1B1B1B] border-collapse">
                    <thead>
                        <tr class="bg-[#A8D4FF] border-b-[1.5px] border-[#1B1B1B] font-bold">
                            <th class="p-4">Anak & Orang Tua</th>
                            <th class="p-4">Data Penjemput</th>
                            <th class="p-4">Jadwal Jemput</th>
                            <th class="p-4">Identitas KTP</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Catatan</th>
                            <th class="p-4 text-center">Validasi Pengasuh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1B1B1B]">
                        @foreach($penjemputanList as $item)
                            <tr class="{{ $loop->even ? 'bg-[#FFF6D6]' : 'bg-white' }}">
                                {{-- Anak & Orang Tua --}}
                                <td class="p-4">
                                    <div class="font-bold text-[#1B1B1B]">{{ $item->anak->nama_lengkap ?? '-' }}</div>
                                    <div class="text-xs text-[#6B6B6B]">
                                        Kelas: {{ $item->anak->kelas->nama_kelas ?? '-' }} · Ortu: {{ $item->user->name ?? '-' }}
                                    </div>
                                </td>

                                {{-- Data Penjemput --}}
                                <td class="p-4">
                                    @if($item->penjemput)
                                        <div class="font-semibold">{{ $item->penjemput->nama }} ({{ $item->penjemput->hubungan }})</div>
                                        <div class="text-xs text-[#6B6B6B]">NIK: {{ $item->penjemput->nik }} · HP: {{ $item->penjemput->no_hp }}</div>
                                    @else
                                        <span class="text-xs text-[#6B6B6B]">-</span>
                                    @endif
                                </td>

                                {{-- Jadwal Jemput --}}
                                <td class="p-4">
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($item->waktu_jemput)->translatedFormat('d M Y') }}</div>
                                    <div class="text-xs text-[#6B6B6B]">{{ \Carbon\Carbon::parse($item->waktu_jemput)->format('H:i') }} WIB</div>
                                </td>

                                {{-- Bukti KTP --}}
                                <td class="p-4">
                                    @if($item->foto_bukti)
                                        <a href="{{ asset('storage/' . $item->foto_bukti) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-[6px] bg-white border border-[#1B1B1B] hover:bg-[#FFD43B] transition-colors">
                                            🖼️ Cek KTP
                                        </a>
                                    @else
                                        <span class="text-xs text-[#6B6B6B] italic">Kosong</span>
                                    @endif
                                </td>

                                {{-- Badge Status --}}
                                <td class="p-4">
                                    @php
                                        $statusClass = [
                                            'pending'    => 'bg-[#FFD43B] text-[#1B1B1B]',
                                            'disetujui'  => 'bg-[#A8E6CF] text-[#1B1B1B]',
                                            'ditolak'    => 'bg-[#FF9E80] text-[#1B1B1B]',
                                            'selesai'    => 'bg-[#A8D4FF] text-[#1B1B1B]',
                                            'dibatalkan' => 'bg-[#1B1B1B] text-white',
                                        ][$item->status] ?? 'bg-gray-200 text-[#1B1B1B]';

                                        $statusLabel = [
                                            'pending'    => '⏳ Menunggu',
                                            'disetujui'  => '✅ Disetujui',
                                            'ditolak'    => '❌ Ditolak',
                                            'selesai'    => '🎉 Selesai',
                                            'dibatalkan' => '🚫 Dibatalkan',
                                        ][$item->status] ?? ucfirst($item->status);
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full border border-[#1B1B1B] {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Catatan --}}
                                <td class="p-4 text-xs text-[#6B6B6B]">
                                    {{ $item->catatan ?? '-' }}
                                </td>

                                {{-- Aksi Validasi (PBI-17e) --}}
                                <td class="p-4 text-center">
                                    @if($item->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button"
                                                    onclick="openValidasiModal({{ $item->id }}, '{{ addslashes($item->anak->nama_lengkap ?? '') }}', '{{ addslashes($item->penjemput->nama ?? '') }}', 'disetujui')"
                                                    class="px-3 py-1.5 text-xs font-semibold rounded-[10px] bg-[#A8E6CF] text-[#1B1B1B] border border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                                ✓ Setujui
                                            </button>
                                            <button type="button"
                                                    onclick="openValidasiModal({{ $item->id }}, '{{ addslashes($item->anak->nama_lengkap ?? '') }}', '{{ addslashes($item->penjemput->nama ?? '') }}', 'ditolak')"
                                                    class="px-3 py-1.5 text-xs font-semibold rounded-[10px] bg-[#FF9E80] text-[#1B1B1B] border border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                                ✕ Tolak
                                            </button>
                                        </div>
                                    @elseif($item->status === 'disetujui')
                                        <button type="button"
                                                onclick="openValidasiModal({{ $item->id }}, '{{ addslashes($item->anak->nama_lengkap ?? '') }}', '{{ addslashes($item->penjemput->nama ?? '') }}', 'selesai')"
                                                class="px-3 py-1.5 text-xs font-semibold rounded-[10px] bg-[#FFD43B] text-[#1B1B1B] border border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                                            🤝 Serahkan Anak
                                        </button>
                                    @else
                                        <span class="text-xs text-[#6B6B6B]">Selesai Diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($penjemputanList->hasPages())
                <div class="p-4 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                    {{ $penjemputanList->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

{{-- MODAL VALIDASI PENGASUH --}}
<div id="validasiModalOverlay" class="fixed inset-0 z-50 hidden bg-[#1B1B1B]/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden">
        <div id="validasiModalHead" class="flex items-center justify-between p-4 bg-[#A8E6CF] border-b-[1.5px] border-[#1B1B1B]">
            <h4 id="validasiModalTitle" class="text-base sm:text-lg font-bold m-0 text-[#1B1B1B]">
                Konfirmasi Validasi
            </h4>
            <button type="button" onclick="closeValidasiModal()" class="w-8 h-8 grid place-items-center rounded-full border border-[#1B1B1B] bg-white font-bold text-sm cursor-pointer">
                ✕
            </button>
        </div>

        <form id="validasiForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="validasiStatusInput" value="">

            <div class="p-5 space-y-4 bg-white">
                <p id="validasiDesc" class="text-sm text-[#1B1B1B] m-0"></p>

                <div class="grid gap-1.5">
                    <label for="validasiCatatan" class="font-semibold text-xs text-[#1B1B1B]">
                        Catatan Pengasuh (Opsional)
                    </label>
                    <textarea id="validasiCatatan" name="catatan" rows="2"
                              placeholder="Contoh: KTP sudah dicocokkan langsung di gerbang."
                              class="w-full text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-2.5 focus:outline-none focus:bg-[#FFF6D6]"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 p-4 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                <button type="button" onclick="closeValidasiModal()" class="px-4 py-2 font-semibold text-sm rounded-[10px] bg-white text-[#1B1B1B] border border-[#1B1B1B] cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="validasiSubmitBtn" class="px-4 py-2 font-semibold text-sm rounded-[10px] bg-[#FFD43B] text-[#1B1B1B] border border-[#1B1B1B] hover:-translate-y-0.5 transition-transform cursor-pointer">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openValidasiModal(id, anakName, penjemputName, targetStatus) {
        const overlay = document.getElementById('validasiModalOverlay');
        const form = document.getElementById('validasiForm');
        const head = document.getElementById('validasiModalHead');
        const title = document.getElementById('validasiModalTitle');
        const desc = document.getElementById('validasiDesc');
        const statusInput = document.getElementById('validasiStatusInput');
        const submitBtn = document.getElementById('validasiSubmitBtn');

        form.action = "{{ url('/pengasuh/penjemputan') }}/" + id + "/validasi";
        statusInput.value = targetStatus;

        if (targetStatus === 'disetujui') {
            head.style.backgroundColor = '#A8E6CF';
            title.textContent = '✅ Setujui Izin Penjemputan';
            desc.innerHTML = `Setujui penjemputan anak <b>${anakName}</b> oleh <b>${penjemputName}</b>?`;
            submitBtn.style.backgroundColor = '#A8E6CF';
            submitBtn.textContent = 'Ya, Setujui';
        } else if (targetStatus === 'ditolak') {
            head.style.backgroundColor = '#FF9E80';
            title.textContent = '❌ Tolak Izin Penjemputan';
            desc.innerHTML = `Tolak pengajuan penjemputan anak <b>${anakName}</b> oleh <b>${penjemputName}</b>?`;
            submitBtn.style.backgroundColor = '#FF9E80';
            submitBtn.textContent = 'Ya, Tolak Izin';
        } else {
            head.style.backgroundColor = '#FFD43B';
            title.textContent = '🤝 Konfirmasi Penyerahan Anak';
            desc.innerHTML = `Konfirmasi bahwa <b>${anakName}</b> telah diserahkan dengan aman kepada <b>${penjemputName}</b>?`;
            submitBtn.style.backgroundColor = '#FFD43B';
            submitBtn.textContent = 'Selesaikan Penjemputan';
        }

        overlay.classList.remove('hidden');
    }

    function closeValidasiModal() {
        document.getElementById('validasiModalOverlay').classList.add('hidden');
    }

    document.getElementById('validasiModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeValidasiModal();
    });
</script>
@endsection