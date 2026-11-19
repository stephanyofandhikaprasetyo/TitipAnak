<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan cache permission sebelum memulai
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $matrix = [
            'admin' => [
                'kelola akun',
                'kelola master data',
                'verifikasi pendaftaran',
                'verifikasi pembayaran',
                'kelola tagihan',
                'kelola jadwal',
                'kelola pengumuman',
                'lihat semua laporan',
                'hapus data kehadiran',
                'lihat dashboard admin',
            ],
            'pengasuh' => [
                'presensi anak',
                'buat laporan harian',
                'buat laporan insiden',
                'buat penilaian bulanan',
                'validasi izin penjemputan',
                'lihat jadwal sendiri',
                'lihat catatan kesehatan',
            ],
            'orang_tua' => [
                'kelola pendaftaran sendiri',
                'kelola data anak',
                'lihat jadwal anak',
                'bayar tagihan',
                'kelola penjemput',
                'ajukan izin penjemputan',
                'lihat laporan anak',
            ],
        ];

        // 2. Kumpulkan dan buat SEMUA permission terlebih dahulu
        $allPermissions = collect($matrix)
            ->flatten()
            ->push('ekspor data')
            ->unique();

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // 3. Reset cache Spatie agar permission yang baru dibuat langsung terbaca
        // (wajib dilakukan karena DatabaseSeeder memakai trait WithoutModelEvents)
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 4. Buat role dan pasangkan permission-nya
        foreach ($matrix as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($permissions);
        }

        // 5. Reset cache terakhir kali
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}