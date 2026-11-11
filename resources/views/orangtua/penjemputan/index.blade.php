@extends('layouts.app')

@section('title', 'Status Izin Penjemputan - TitipAnak')

@section('content')
<div class="max-w-[1120px] mx-auto p-4 sm:p-6 space-y-6">

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-[1.5px] border-[#1B1B1B] pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full border border-[#1B1B1B] bg-[#A8E6CF] text-[#1B1B1B]">
                    👨‍👩‍👧 Orang Tua: {{ auth()->user()->name ?? 'Orang Tua' }}
                </span>
                <span class="text-xs text-[#6B6B6B]">Monitoring Penjemputan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#1B1B1B]">Status Izin Penjemputan</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orangtua.penjemputan.create') }}" 
               class="inline-flex items-center justify-center gap-2 font-semibold text-sm px-5 py-3 rounded-[10px] bg-[#FFD43B] text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 active:translate-y-0 transition-transform">
                <span class="text-lg">➕</span> Ajukan Izin Baru
            </a>

            {{-- Tombol Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 font-semibold text-sm px-4 py-3 rounded-[10px] bg-white text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] hover:bg-[#FF9E80] transition-colors cursor-pointer">
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

    @if(session('error'))
        <div class="flex items-center gap-3 p-4 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] text-sm font-medium">
            <span class="text-xl">⚠️</span>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif

    {{-- State: Belum Ada Data --}}
    @if($penjemputanList->isEmpty())
        <div class="p-12 text-center rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-[#FFF6D6]">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-white border-[1.5px] border-[#1B1B1B] grid place-items-center text-2xl">
                📋
            </div>
            <h3 class="text-lg font-bold text-[#1B1B1B]">Belum Ada Pengajuan Izin</h3>
            <p class="text-sm text-[#6B6B6B] mt-1 max-w-md mx-auto">
                Anda belum pernah mengajukan izin penjemputan darurat/khusus. Klik tombol di atas untuk membuat pengajuan baru.
            </p>
        </div>
    @else
        {{-- State: Tabel Status Penjemputan --}}
        <div class="rounded-[10px] border-[1.5px] border-[#1B1B1B] bg-white overflow-hidden shadow-none">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#1B1B1B] border-collapse">
                    <thead>
                        <tr class="bg-[#FFD43B] border-b-[1.5px] border-[#1B1B1B] font-bold">
                            <th class="p-4">Anak</th>
                            <th class="p-4">Penjemput</th>
                            <th class="p-4">Waktu & Tanggal</th>
                            <th class="p-4">Bukti Identitas</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Catatan</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1B1B1B]">
                        @foreach($penjemputanList as $item)
                            <tr class="{{ $loop->even ? 'bg-[#FFF6D6]' : 'bg-white' }}">
                                {{-- Detail Anak --}}
                                <td class="p-4 font-semibold">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#A8D4FF] border border-[#1B1B1B] grid place-items-center font-bold text-xs">
                                            {{ strtoupper(substr($item->anak->nama_panggilan ?? $item->anak->nama_lengkap ?? 'AN', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-[#1B1B1B]">{{ $item->anak->nama_lengkap ?? 'Data Anak Tidak Ditemukan' }}</div>
                                            <div class="text-xs text-[#6B6B6B]">Kelas: {{ $item->anak->kelas->nama_kelas ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Detail Penjemput --}}
                                <td class="p-4">
                                    @if($item->penjemput)
                                        <div class="font-semibold">{{ $item->penjemput->nama }}</div>
                                        <div class="text-xs text-[#6B6B6B]">Rutin ({{ $item->penjemput->hubungan }})</div>
                                    @else
                                        <div class="font-semibold text-[#1B1B1B]">Penjemput Darurat</div>
                                        <div class="text-xs text-[#6B6B6B]">Pengajuan Khusus Orang Tua</div>
                                    @endif
                                </td>

                                {{-- Waktu Penjemputan --}}
                                <td class="p-4">
                                    <div class="font-medium">
                                        {{ \Carbon\Carbon::parse($item->waktu_jemput)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-xs text-[#6B6B6B]">
                                        Jam: {{ \Carbon\Carbon::parse($item->waktu_jemput)->format('H:i') }} WIB
                                    </div>
                                </td>

                                {{-- Upload Bukti / Foto KTP --}}
                                <td class="p-4">
                                    @if($item->foto_bukti)
                                        <a href="{{ asset('storage/' . $item->foto_bukti) }}" target="_blank" 
                                           class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-[6px] bg-[#FFF6D6] border border-[#1B1B1B] hover:bg-[#FFD43B] transition-colors">
                                            🖼️ Lihat Identitas
                                        </a>
                                    @else
                                        <span class="text-xs text-[#6B6B6B] italic">Tidak ada foto</span>
                                    @endif
                                </td>

                                {{-- Status Badge --}}
                                <td class="p-4">
                                    @php
                                        $statusClass = [
                                            'pending'   => 'bg-[#FFD43B] text-[#1B1B1B]',
                                            'disetujui' => 'bg-[#A8E6CF] text-[#1B1B1B]',
                                            'ditolak'   => 'bg-[#FF9E80] text-[#1B1B1B]',
                                            'selesai'   => 'bg-[#A8D4FF] text-[#1B1B1B]',
                                            'dibatalkan'=> 'bg-[#1B1B1B] text-white',
                                        ][$item->status] ?? 'bg-gray-200 text-[#1B1B1B]';

                                        $statusLabel = [
                                            'pending'   => '⏳ Menunggu Verifikasi',
                                            'disetujui' => '✅ Disetujui',
                                            'ditolak'   => '❌ Ditolak',
                                            'selesai'   => '🎉 Selesai',
                                            'dibatalkan'=> '🚫 Dibatalkan',
                                        ][$item->status] ?? ucfirst($item->status);
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full border border-[#1B1B1B] {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Catatan Admin/Guru --}}
                                <td class="p-4 text-xs text-[#6B6B6B]">
                                    {{ $item->catatan ?? '-' }}
                                </td>

                                {{-- Aksi Pembatalan (Tampil jika status 'pending' ATAU 'disetujui') --}}
                                <td class="p-4 text-center">
                                    @if(in_array($item->status, ['pending', 'disetujui']))
                                        <button type="button" 
                                                onclick="openCancelModal({{ $item->id }}, '{{ addslashes($item->anak->nama_lengkap ?? 'Anak') }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-[10px] bg-[#FF9E80] text-[#1B1B1B] border border-[#1B1B1B] hover:-translate-y-0.5 active:translate-y-0 transition-transform cursor-pointer">
                                            🚫 Batalkan
                                        </button>
                                    @else
                                        <span class="text-xs text-[#6B6B6B]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            @if($penjemputanList->hasPages())
                <div class="p-4 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                    {{ $penjemputanList->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

{{-- MODAL KONFIRMASI PEMBATALAN (OVERLAY MODAL SYSTEM) --}}
<div id="cancelModalOverlay" class="fixed inset-0 z-50 hidden bg-[#1B1B1B]/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] overflow-hidden shadow-none">
        
        {{-- Header Modal Coral --}}
        <div class="flex items-center justify-between p-4 bg-[#FF9E80] border-b-[1.5px] border-[#1B1B1B]">
            <h4 class="text-base sm:text-lg font-bold m-0 flex items-center gap-2 text-[#1B1B1B]">
                ⚠️ Batalkan Pengajuan Izin
            </h4>
            <button type="button" onclick="closeCancelModal()" class="w-8 h-8 grid place-items-center rounded-full border border-[#1B1B1B] bg-white font-bold text-sm cursor-pointer hover:bg-gray-100">
                ✕
            </button>
        </div>

        {{-- Form Action --}}
        <form id="cancelForm" method="POST" action="">
            @csrf
            @method('PATCH')
            
            <div class="p-5 space-y-3 bg-white">
                <p class="text-sm text-[#1B1B1B] m-0">
                    Apakah Anda yakin ingin membatalkan pengajuan izin penjemputan untuk <b id="modalChildName" class="underline text-[#1B1B1B]"></b>?
                </p>
                <p class="text-xs text-[#6B6B6B] m-0">
                    Status pengajuan akan diubah menjadi <b>dibatalkan</b> dan tidak dapat dikembalikan.
                </p>
            </div>

            {{-- Footer Modal --}}
            <div class="flex justify-end gap-3 p-4 bg-[#FFF6D6] border-t-[1.5px] border-[#1B1B1B]">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 font-semibold text-sm rounded-[10px] bg-white text-[#1B1B1B] border border-[#1B1B1B] hover:bg-gray-100 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 font-semibold text-sm rounded-[10px] bg-[#FF9E80] text-[#1B1B1B] border border-[#1B1B1B] hover:-translate-y-0.5 active:translate-y-0 transition-transform cursor-pointer">
                    Ya, Batalkan Izin
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal(id, childName) {
        const overlay = document.getElementById('cancelModalOverlay');
        const form = document.getElementById('cancelForm');
        const nameSpan = document.getElementById('modalChildName');

        // Dynamically set form route
        form.action = "{{ url('/orangtua/penjemputan') }}/" + id + "/cancel";
        nameSpan.textContent = childName;

        // Show Modal
        overlay.classList.remove('hidden');
    }

    function closeCancelModal() {
        const overlay = document.getElementById('cancelModalOverlay');
        overlay.classList.add('hidden');
    }

    // Close when clicking overlay backdrop
    document.getElementById('cancelModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancelModal();
        }
    });
</script>
@endsection