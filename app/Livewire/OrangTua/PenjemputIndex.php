<?php

namespace App\Livewire\OrangTua;

use App\Models\Anak;
use App\Models\IzinPenjemputan;
use App\Models\Kehadiran;
use App\Models\PenjemputTerdaftar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.orangtua')]
#[Title('Penjemputan Anak')]
class PenjemputIndex extends Component
{
    use WithFileUploads, WithPagination;

    // State Panel Form Terpadu
    public bool $showForm = false;
    public ?int $anak_id = null;
    public string $mode_penjemput = 'baru';
    public ?int $penjemput_id = null;
    public string $nama = '';
    public string $hubungan = '';
    public string $no_hp = '';
    public bool $simpan_wali = true;
    public string $tanggal_jemput = '';
    public string $alasan = '';
    public $foto_bukti;

    // State Filter & Pencarian
    public string $q = '';
    public string $filterAnakId = '';
    public string $filterStatus = '';

    // State Modal Detail & Pembatalan
    public ?array $detailItem = null;
    public ?array $cancelTarget = null;

    public function mount(): void
    {
        $this->tanggal_jemput = now()->format('Y-m-d');
    }

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAnakId(): void
    {
        $this->resetPage();
    }

    public function setFilterStatus(string $status): void
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function resetFilter(): void
    {
        $this->q = '';
        $this->filterAnakId = '';
        $this->filterStatus = '';
        $this->resetPage();
    }

    public function toggleForm(?int $anakId = null): void
    {
        if ($anakId !== null) {
            $this->anak_id = $anakId;
            $this->penjemput_id = null;
            $this->showForm = true;
            $this->resetValidation();
            return;
        }

        $this->showForm = ! $this->showForm;
        $this->resetValidation();
    }

    public function updatedAnakId(): void
    {
        $this->penjemput_id = null;
    }

    public function updatedModePenjemput(): void
    {
        $this->resetValidation(['penjemput_id', 'nama', 'hubungan', 'no_hp', 'foto_bukti']);
    }

    public function simpanPengajuan(): void
    {
        $userId = Auth::id();

        $validated = $this->validate([
            'anak_id'        => ['required', 'exists:anak,id'],
            'mode_penjemput' => ['required', 'in:baru,terdaftar'],
            'penjemput_id'   => ['required_if:mode_penjemput,terdaftar', 'nullable', 'exists:penjemput_terdaftar,id'],
            'nama'           => ['required_if:mode_penjemput,baru', 'nullable', 'string', 'max:100'],
            'hubungan'       => ['required_if:mode_penjemput,baru', 'nullable', 'string', 'max:50'],
            'no_hp'          => ['required_if:mode_penjemput,baru', 'nullable', 'string', 'max:20'],
            'tanggal_jemput' => ['required', 'date', 'after_or_equal:today'],
            'alasan'         => ['required', 'string', 'max:1000'],
            'foto_bukti'     => ['required_if:mode_penjemput,baru', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'anak_id.required'            => 'Silakan pilih anak yang akan dijemput.',
            'penjemput_id.required_if'    => 'Silakan pilih wali penjemput terdaftar.',
            'nama.required_if'            => 'Nama lengkap penjemput wajib diisi.',
            'hubungan.required_if'        => 'Hubungan dengan anak wajib diisi.',
            'no_hp.required_if'           => 'Nomor HP penjemput wajib diisi.',
            'foto_bukti.required_if'      => 'Foto identitas/KTP wajib diunggah untuk penjemput baru.',
            'alasan.required'             => 'Alasan atau keterangan penjemputan wajib diisi.',
        ]);

        $anak = Anak::where('id', $validated['anak_id'])
            ->where('orang_tua_id', $userId)
            ->firstOrFail();

        DB::transaction(function () use ($validated, $anak, $userId) {
            $pathFoto = null;
            if ($this->foto_bukti) {
                $pathFoto = $this->foto_bukti->store('penjemput-identitas', 'public');
            }

            if ($validated['mode_penjemput'] === 'terdaftar') {
                $wali = PenjemputTerdaftar::where('id', $validated['penjemput_id'])
                    ->where('anak_id', $anak->id)
                    ->firstOrFail();

                $namaPenjemput = $wali->nama_lengkap;
                $hubungan      = $wali->hubungan;
                $noHp          = $wali->no_hp;
                $fotoFinal     = $pathFoto ?? $wali->path_foto_identitas ?? '';
                $waliId        = $wali->id;
            } else {
                $namaPenjemput = $validated['nama'];
                $hubungan      = $validated['hubungan'];
                $noHp          = $validated['no_hp'];
                $fotoFinal     = $pathFoto ?? '';
                $waliId        = null;

                if ($this->simpan_wali) {
                    $waliBaru = PenjemputTerdaftar::create([
                        'anak_id'             => $anak->id,
                        'nama_lengkap'        => $namaPenjemput,
                        'hubungan'            => $hubungan,
                        'no_hp'               => $noHp,
                        'path_foto_identitas' => $fotoFinal,
                        'aktif'               => 1,
                    ]);
                    $waliId = $waliBaru->id;
                }
            }

            IzinPenjemputan::create([
                'anak_id'                => $anak->id,
                'diajukan_oleh_id'       => $userId,
                'penjemput_terdaftar_id' => $waliId,
                'nama_penjemput'         => $namaPenjemput,
                'hubungan_penjemput'     => $hubungan,
                'no_hp_penjemput'        => $noHp,
                'path_foto_identitas'    => $fotoFinal,
                'alasan'                 => $validated['alasan'],
                'tanggal_jemput'         => $validated['tanggal_jemput'],
                'status'                 => 'menunggu',
            ]);
        });

        $this->reset(['anak_id', 'penjemput_id', 'nama', 'hubungan', 'no_hp', 'alasan', 'foto_bukti', 'showForm']);
        $this->mode_penjemput = 'baru';
        $this->simpan_wali = true;
        $this->tanggal_jemput = now()->format('Y-m-d');

        session()->flash('success', 'Pengajuan izin penjemputan berhasil dikirim dan menunggu verifikasi pengasuh.');
    }

    public function bukaDetail(int $izinId): void
    {
        $item = IzinPenjemputan::with(['anak', 'validator'])
            ->where('diajukan_oleh_id', Auth::id())
            ->findOrFail($izinId);

        $fotoUrl = '';
        if ($item->path_foto_identitas) {
            $fotoUrl = Str::startsWith($item->path_foto_identitas, ['http://', 'https://'])
                ? $item->path_foto_identitas
                : asset('storage/' . $item->path_foto_identitas);
        }

        $this->detailItem = [
            'anak'      => $item->anak?->nama_lengkap ?? '-',
            'status'    => ucfirst($item->status),
            'penjemput' => $item->nama_penjemput,
            'hubungan'  => ucfirst((string) ($item->hubungan_penjemput ?? 'Wali')),
            'hp'        => $item->no_hp_penjemput ?? '-',
            'waktu'     => $item->tanggal_jemput?->translatedFormat('d M Y') ?? '-',
            'validator' => $item->validator?->nama ?? 'Belum divalidasi',
            'alasan'    => $item->alasan,
            'foto'      => $fotoUrl,
        ];
    }

    public function tutupDetail(): void
    {
        $this->detailItem = null;
    }

    public function konfirmasiBatal(int $izinId): void
    {
        $item = IzinPenjemputan::with('anak')
            ->where('diajukan_oleh_id', Auth::id())
            ->where('status', 'menunggu')
            ->findOrFail($izinId);

        $this->cancelTarget = [
            'id'        => $item->id,
            'anak'      => $item->anak?->nama_lengkap ?? 'Anak',
            'penjemput' => $item->nama_penjemput,
        ];
    }

    public function tutupBatal(): void
    {
        $this->cancelTarget = null;
    }

    public function batalkanPengajuan(): void
    {
        if (! $this->cancelTarget) {
            return;
        }

        $item = IzinPenjemputan::where('diajukan_oleh_id', Auth::id())
            ->where('id', $this->cancelTarget['id'])
            ->firstOrFail();

        if ($item->status === 'menunggu') {
            $item->update(['status' => 'dibatalkan']);
            session()->flash('success', 'Pengajuan penjemputan berhasil dibatalkan.');
        }

        $this->cancelTarget = null;
    }

    public function render()
    {
        $userId = Auth::id();

        $daftarAnak = Anak::with([
                'kelompokUsia',
                'catatanKesehatan',
                'penjemputTerdaftar' => fn ($q) => $q->where('aktif', 1),
            ])
            ->where('orang_tua_id', $userId)
            ->orderBy('nama_lengkap')
            ->get();

        $kehadiranHariIni = Kehadiran::whereIn('anak_id', $daftarAnak->pluck('id'))
            ->whereDate('tanggal', today())
            ->get()
            ->keyBy('anak_id');

        $daftarAnak->each(function ($anak) use ($kehadiranHariIni) {
            $hadir = $kehadiranHariIni->get($anak->id);
            if ($anak->status !== 'aktif') {
                $anak->status_kehadiran_label = 'Belum Aktif';
                $anak->status_kehadiran_tone  = 'default';
                $anak->bisa_diajukan          = false;
            } elseif ($hadir && $hadir->check_out_pada) {
                $anak->status_kehadiran_label = 'Sudah Dijemput';
                $anak->status_kehadiran_tone  = 'blue';
                $anak->bisa_diajukan          = false;
            } elseif ($hadir) {
                $anak->status_kehadiran_label = 'Hadir di Daycare';
                $anak->status_kehadiran_tone  = 'mint';
                $anak->bisa_diajukan          = true;
            } else {
                $anak->status_kehadiran_label = 'Belum Check-In';
                $anak->status_kehadiran_tone  = 'yellow';
                $anak->bisa_diajukan          = true;
            }
        });

        $anakAktifBisaJemput = $daftarAnak->where('bisa_diajukan', true)->values();

        // Daftar wali terdaftar untuk anak yang sedang dipilih pada form
        $waliAnakTerpilih = collect();
        if ($this->anak_id) {
            $anakTerpilih = $anakAktifBisaJemput->firstWhere('id', $this->anak_id);
            $waliAnakTerpilih = $anakTerpilih ? $anakTerpilih->penjemputTerdaftar : collect();
        }

        $anakSedangDiajukanIds = IzinPenjemputan::where('diajukan_oleh_id', $userId)
            ->where('status', 'menunggu')
            ->pluck('anak_id')
            ->all();

        $baseStat = IzinPenjemputan::where('diajukan_oleh_id', $userId);
        $statistik = [
            'total'      => (clone $baseStat)->count(),
            'menunggu'   => (clone $baseStat)->where('status', 'menunggu')->count(),
            'disetujui'  => (clone $baseStat)->where('status', 'disetujui')->count(),
            'ditolak'    => (clone $baseStat)->where('status', 'ditolak')->count(),
            'dibatalkan' => (clone $baseStat)->where('status', 'dibatalkan')->count(),
        ];

        $search = trim($this->q);
        $riwayatPenjemputan = IzinPenjemputan::with(['anak.kelompokUsia', 'validator'])
            ->where('diajukan_oleh_id', $userId)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_penjemput', 'like', "%{$search}%")
                        ->orWhereHas('anak', fn ($a) => $a->where('nama_lengkap', 'like', "%{$search}%"));
                });
            })
            ->when($this->filterAnakId !== '', fn ($q) => $q->where('anak_id', $this->filterAnakId))
            ->when($this->filterStatus !== '', fn ($q) => $q->where('status', $this->filterStatus))
            ->orderByDesc('tanggal_jemput')
            ->orderByDesc('id')
            ->paginate(10);

        $daftarPenjemput = PenjemputTerdaftar::with('anak')
            ->whereIn('anak_id', $daftarAnak->pluck('id'))
            ->where('aktif', 1)
            ->orderBy('nama_lengkap')
            ->get();

        $daftarStatus = [
            'menunggu'   => 'Menunggu',
            'disetujui'  => 'Disetujui',
            'ditolak'    => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
        ];

        return view('livewire.orang-tua.penjemput-index', compact(
            'daftarAnak',
            'anakAktifBisaJemput',
            'waliAnakTerpilih',
            'anakSedangDiajukanIds',
            'statistik',
            'riwayatPenjemputan',
            'daftarPenjemput',
            'daftarStatus'
        ));
    }
}