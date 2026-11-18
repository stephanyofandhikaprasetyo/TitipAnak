-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2026 at 05:16 AM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `titipanak`
--

-- --------------------------------------------------------

--
-- Table structure for table `anak`
--

CREATE TABLE `anak` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `kelas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode_anak` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nama_panggilan` varchar(50) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `catatan_medis` text DEFAULT NULL,
  `status_kehadiran` enum('belum_datang','hadir','dijemput','pulang') NOT NULL DEFAULT 'belum_datang',
  `jam_masuk` datetime DEFAULT NULL,
  `jam_pulang` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anak`
--

INSERT INTO `anak` (`id`, `user_id`, `kelas_id`, `kode_anak`, `nama_lengkap`, `nama_panggilan`, `jenis_kelamin`, `tanggal_lahir`, `tempat_lahir`, `foto`, `alamat`, `catatan_medis`, `status_kehadiran`, `jam_masuk`, `jam_pulang`, `is_active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(5, 1, 1, 'ANK-2026-001', 'Aisha Nayla Putri', 'Aisha', 'P', '2023-05-14', 'Bandung', 'anak/aisha.jpg', 'Jl. Sukabirus No. 12, Dayeuhkolot, Bandung', 'Alergi makanan laut (seafood)', 'hadir', '2026-11-09 07:30:00', NULL, 1, NULL, '2026-11-09 09:19:55', '2026-11-09 09:19:55'),
(6, 1, 1, 'ANK-2026-002', 'Siti Anindya Humaira', 'Anin', 'P', '2023-08-20', 'Bandung', 'anak/anin.jpg', 'Jl. Telekomunikasi No. 45, Bojongsoang, Bandung', 'Tidak ada riwayat alergi', 'belum_datang', NULL, NULL, 1, NULL, '2026-11-09 09:19:55', '2026-11-09 09:19:55'),
(7, 4, 2, 'ANK-2026-003', 'Muhammad Rafi Alfarizi', 'Rafi', 'L', '2024-09-10', 'Bandung', 'anak/rafi.jpg', 'Jl. Buah Batu No. 88, Bandung', 'Alergi susu sapi (pakai susu soya)', 'hadir', '2026-11-13 07:15:00', NULL, 1, NULL, '2026-11-12 01:05:00', '2026-11-13 00:15:00'),
(8, 4, 3, 'ANK-2026-004', 'Keisha Amelia Putri', 'Keisha', 'P', '2022-03-22', 'Cimahi', 'anak/keisha.jpg', 'Jl. Buah Batu No. 88, Bandung', 'Tidak ada riwayat alergi', 'hadir', '2026-11-13 07:20:00', NULL, 1, NULL, '2026-11-12 01:05:00', '2026-11-13 00:20:00'),
(9, 5, 1, 'ANK-2026-005', 'Arka Wiratama', 'Arka', 'L', '2023-11-05', 'Jakarta', 'anak/arka.jpg', 'Komplek Permata Buah Batu Blok C4', 'Asma ringan, bawa inhaler di tas', 'hadir', '2026-11-13 08:00:00', NULL, 1, NULL, '2026-11-12 01:10:00', '2026-11-13 01:00:00'),
(10, 6, 3, 'ANK-2026-006', 'Nayla Azzahra', 'Nayla', 'P', '2022-07-18', 'Bandung', 'anak/nayla.jpg', 'Jl. Ciganitri No. 19, Bojongsoang', 'Alergi kacang tanah', 'dijemput', '2026-11-13 07:45:00', '2026-11-13 10:30:00', 1, NULL, '2026-11-12 01:15:00', '2026-11-13 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Kelas Playgroup A', 'Kelas anak usia 2-3 tahun', '2026-11-09 09:19:55', '2026-11-09 09:19:55'),
(2, 'Kelas Batita B', 'Kelas khusus anak usia 1-2 tahun', '2026-11-12 01:00:00', '2026-11-12 01:00:00'),
(3, 'Kelas Prasekolah', 'Kelas persiapan TK usia 4-5 tahun', '2026-11-12 01:00:00', '2026-11-12 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_harian`
--

CREATE TABLE `laporan_harian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anak_id` bigint(20) UNSIGNED NOT NULL,
  `pengasuh_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `makan` text NOT NULL,
  `tidur` varchar(255) NOT NULL,
  `bab_bak` varchar(255) NOT NULL,
  `aktivitas` text NOT NULL,
  `kondisi` enum('sehat','kurang_fit','sakit','rewel') NOT NULL DEFAULT 'sehat',
  `catatan_tambahan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporan_harian`
--

INSERT INTO `laporan_harian` (`id`, `anak_id`, `pengasuh_id`, `tanggal`, `makan`, `tidur`, `bab_bak`, `aktivitas`, `kondisi`, `catatan_tambahan`, `created_at`, `updated_at`) VALUES
(1, 5, 3, '2026-11-18', 'Sarapan bubur ayam habis 1 mangkuk, snack pagi pisang ambon habis, makan siang nasi sop ayam & sayur wortel (menu bebas seafood sesuai catatan alergi) habis 1 porsi.', 'Tidur siang nyenyak pukul 12.30 - 14.15 WIB (1 jam 45 menit).', 'BAK 3x normal, BAB 1x pukul 10.15 WIB (konsistensi normal).', 'Mengikuti senam pagi bersama kelas Playgroup A, bermain menyusun balok kayu warna-warni, dan mewarnai gambar hewan.', 'sehat', 'Aisha sangat aktif dan ceria hari ini. Mohon besok membawakan 1 stel baju ganti tambahan.', '2026-11-18 07:30:00', '2026-11-18 07:30:00'),
(2, 7, 3, '2026-11-18', 'Sarapan roti gandum habis 3/4 bagian, minum susu soya bawaan orang tua 1 botol (180 ml), makan siang nasi tim ikan salmon habis 3/4 porsi.', 'Tidur siang pukul 13.00 - 14.00 WIB (sempat terbangun sebentar lalu tidur kembali).', 'BAK 2x normal, belum BAB selama di daycare.', 'Bermain sensory play (pasir kinetik), mendengarkan dongeng boneka tangan, dan latihan motorik halus meronce manik besar.', 'kurang_fit', 'Rafi sempat terlihat agak lemas sebelum makan siang, suhu tubuh normal (36.6°C). Setelah tidur siang kondisi membaik.', '2026-11-18 07:45:00', '2026-11-18 07:45:00'),
(3, 8, 3, '2026-11-18', 'Sarapan nasi goreng sayur habis 1 porsi, snack pagi apel potong habis, makan siang nasi ayam teriyaki & tumis buncis habis.', 'Tidur siang pukul 12.45 - 14.15 WIB.', 'BAK 3x normal, BAB 1x pukul 09.40 WIB.', 'Mengikuti kelas persiapan membaca & berhitung dasar (Kelas Prasekolah), menyusun puzzle 24 keping, dan bernyanyi bersama.', 'sehat', 'Keisha sudah mampu merapikan mainan sendiri setelah selesai bermain.', '2026-11-18 08:00:00', '2026-11-18 08:00:00'),
(4, 9, 3, '2026-11-18', 'Sarapan bubur kacang hijau habis 1 mangkuk kecil, minum air putih cukup, makan siang nasi soto ayam bening habis 1 porsi.', 'Tidur siang pukul 12.30 - 14.00 WIB.', 'BAK 3x normal, belum BAB.', 'Bermain mobil-mobilan di karpet kelas, mendengarkan cerita bergambar, dan latihan motorik menempel kertas origami.', 'sehat', 'Pernapasan Arka stabil sepanjang hari, tas berisi inhaler selalu tersimpan aman di loker pengasuh.', '2026-11-18 08:10:00', '2026-11-18 08:10:00'),
(5, 10, 3, '2026-11-18', 'Sarapan roti tawar selai stroberi (bebas kacang tanah) habis 1 lembar, minum teh hangat manis, makan siang nasi tim ayam habis setengah porsi.', 'Istirahat siang di ruang UKS/tenang pukul 10.00 - 10.30 WIB sebelum dijemput.', 'BAK 2x normal, belum BAB.', 'Mengikuti kegiatan pagi menggambar bebas sebelum suhu tubuh terasa hangat.', 'sakit', 'Suhu tubuh Nayla naik menjadi 37.8°C sekitar pukul 10.00 WIB sehingga orang tua dihubungi untuk penjemputan lebih awal.', '2026-11-18 03:35:00', '2026-11-18 03:35:00'),
(6, 5, 3, '2026-11-17', 'Sarapan nasi uduk telur suwir habis, snack sore biskuit gandum 3 keping, makan siang sup jagung ayam habis.', 'Tidur siang pukul 12.40 - 14.20 WIB.', 'BAK 4x normal, BAB 1x pukul 09.30 WIB.', 'Bernyanyi lagu anak-anak, bermain perosotan di area bermain indoor, dan mengenal bentuk geometri dasar.', 'sehat', NULL, '2026-11-17 08:00:00', '2026-11-17 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_harian_foto`
--

CREATE TABLE `laporan_harian_foto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `laporan_harian_id` bigint(20) UNSIGNED NOT NULL,
  `path_foto` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporan_harian_foto`
--

INSERT INTO `laporan_harian_foto` (`id`, `laporan_harian_id`, `path_foto`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 'laporan-harian/kegiatan/aisha-balok.jpg', 'Aisha bermain menyusun balok kayu bersama teman', '2026-11-18 07:32:00', '2026-11-18 07:32:00'),
(2, 1, 'laporan-harian/kegiatan/aisha-mewarnai.jpg', 'Kegiatan mewarnai gambar hewan di ruang kelas Playgroup A', '2026-11-18 07:33:00', '2026-11-18 07:33:00'),
(3, 2, 'laporan-harian/kegiatan/rafi-sensory.jpg', 'Rafi berlatih motorik halus saat sesi sensory play', '2026-11-18 07:47:00', '2026-11-18 07:47:00'),
(4, 3, 'laporan-harian/kegiatan/keisha-puzzle.jpg', 'Keisha menyelesaikan puzzle gambar di kelas Prasekolah', '2026-11-18 08:05:00', '2026-11-18 08:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_insiden`
--

CREATE TABLE `laporan_insiden` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anak_id` bigint(20) UNSIGNED NOT NULL,
  `pengasuh_id` bigint(20) UNSIGNED NOT NULL,
  `waktu_kejadian` datetime NOT NULL,
  `judul_insiden` varchar(150) NOT NULL,
  `tingkat_keparahan` enum('ringan','sedang','berat') NOT NULL DEFAULT 'ringan',
  `kronologi` text NOT NULL,
  `tindakan_pertolongan` text NOT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `status_penanganan` enum('ditangani','pemantauan','selesai') NOT NULL DEFAULT 'ditangani',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporan_insiden`
--

INSERT INTO `laporan_insiden` (`id`, `anak_id`, `pengasuh_id`, `waktu_kejadian`, `judul_insiden`, `tingkat_keparahan`, `kronologi`, `tindakan_pertolongan`, `foto_bukti`, `status_penanganan`, `created_at`, `updated_at`) VALUES
(1, 5, 3, '2026-11-18 10:15:00', 'Terpeleset ringan di area matras bermain', 'ringan', 'Saat berlari mengejar bola di area bermain indoor beralas matras busa, kaki Aisha tersandung sehingga lutut kiri terbentur ringan dan muncul kemerahan kecil tanpa luka terbuka.', 'Aisha langsung ditenangkan oleh pengasuh, area lutut dibersihkan dengan air bersih dan dikompres dingin selama 10 menit. Anak kembali ceria dan dapat berjalan serta bermain normal.', NULL, 'selesai', '2026-11-18 03:30:00', '2026-11-18 04:00:00'),
(2, 10, 3, '2026-11-18 10:00:00', 'Demam ringan mendadak menjelang sesi bermain siang', 'sedang', 'Pengasuh mendapati dahi Nayla terasa hangat saat transisi kegiatan pagi. Hasil pengukuran termometer menunjukkan suhu 37.8°C dan anak tampak lemas.', 'Nayla dipindahkan ke ruang istirahat yang sejuk, diberikan air putih hangat, dikompres air hangat pada dahi, dan orang tua langsung dihubungi hingga dijemput pada pukul 10.30 WIB.', NULL, 'selesai', '2026-11-18 03:10:00', '2026-11-18 03:35:00'),
(3, 7, 3, '2026-11-18 11:10:00', 'Tergores ringan mainan balok plastik di jari telunjuk', 'ringan', 'Saat bermain menyusun balok plastik bersama teman di kelas Batita B, jari telunjuk tangan kanan Rafi tergores sudut balok sehingga timbul goresan tipis sepanjang 0,5 cm.', 'Luka dibersihkan menggunakan cairan antiseptik ramah anak dan ditutup plester karakter. Rafi tidak rewel dan kembali beraktivitas seperti biasa.', NULL, 'pemantauan', '2026-11-18 04:15:00', '2026-11-18 04:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `penjemput`
--

CREATE TABLE `penjemput` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anak_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `hubungan` varchar(50) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjemput`
--

INSERT INTO `penjemput` (`id`, `anak_id`, `nama`, `hubungan`, `nik`, `no_hp`, `foto`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 5, 'stephan yopan', 'bibi', '3276011004030009', '088977506606', 'penjemputan/identitas/ts5xxEJQAMF7f4lioS0guWnt7GxiG7kpcjw8m7jb.jpg', 1, '2026-11-09 02:20:30', '2026-11-09 02:20:30'),
(2, 6, 'stephan yopan', 'nenek', '3276011004030009', '088977506606', 'penjemputan/identitas/bHfZTMwhlqcLl0laBED4SY9XGS7SSym5KfI3ee99.jpg', 1, '2026-11-11 11:35:55', '2026-11-11 11:35:55'),
(3, 7, 'Hendra Wijaya', 'Kakek', '3273011508650002', '081223344556', 'penjemputan/identitas/ktp_hendra.jpg', 1, '2026-11-12 02:00:00', '2026-11-12 02:00:00'),
(4, 8, 'Sari Mulyani', 'Tante', '3273015004920007', '085798765432', 'penjemputan/identitas/ktp_sari.jpg', 1, '2026-11-12 02:05:00', '2026-11-12 02:05:00'),
(5, 9, 'Deni Kurniawan', 'Paman', '3204051212880003', '081345678901', 'penjemputan/identitas/ktp_deni.jpg', 1, '2026-11-12 02:10:00', '2026-11-12 02:10:00'),
(6, 10, 'Mbak Yanti', 'Pengasuh Rumah', '3204084506950001', '089612345678', 'penjemputan/identitas/ktp_yanti.jpg', 1, '2026-11-12 02:15:00', '2026-11-12 02:15:00'),
(7, 5, 'Ratna Dewi', 'Nenek', '3276016002680004', '081122334455', 'penjemputan/identitas/ktp_ratna.jpg', 1, '2026-11-12 02:20:00', '2026-11-12 02:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `penjemputan`
--

CREATE TABLE `penjemputan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anak_id` bigint(20) UNSIGNED NOT NULL,
  `penjemput_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `waktu_jemput` datetime NOT NULL,
  `metode_verifikasi` varchar(50) NOT NULL DEFAULT 'ktp',
  `foto_bukti` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak','selesai','dibatalkan') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjemputan`
--

INSERT INTO `penjemputan` (`id`, `anak_id`, `penjemput_id`, `user_id`, `waktu_jemput`, `metode_verifikasi`, `foto_bukti`, `catatan`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 1, '2026-11-09 10:20:00', 'ktp', 'penjemputan/identitas/ts5xxEJQAMF7f4lioS0guWnt7GxiG7kpcjw8m7jb.jpg', 'masuk anginn', 'disetujui', '2026-11-09 02:20:30', '2026-11-09 02:20:30'),
(2, 6, 2, 1, '2026-11-11 19:35:00', 'ktp', 'penjemputan/identitas/bHfZTMwhlqcLl0laBED4SY9XGS7SSym5KfI3ee99.jpg', 'uwawdadadadadad', 'dibatalkan', '2026-11-11 11:35:55', '2026-11-11 11:41:52'),
(3, 5, 1, 1, '2026-11-13 15:00:00', 'ktp', 'penjemputan/identitas/ts5xxEJQAMF7f4lioS0guWnt7GxiG7kpcjw8m7jb.jpg', 'Jemput lebih awal karena jadwal kontrol dokter', 'pending', '2026-11-13 08:00:00', '2026-11-13 08:00:00'),
(4, 7, 3, 4, '2026-11-13 13:30:00', 'ktp', 'penjemputan/identitas/ktp_hendra.jpg', 'Kakek jemput lebih awal karena ada acara keluarga di Lembang', 'pending', '2026-11-13 01:15:00', '2026-11-13 01:15:00'),
(5, 8, 4, 4, '2026-11-13 16:00:00', 'ktp', 'penjemputan/identitas/ktp_sari.jpg', 'Orang tua lembur kantor, dijemput Tante Sari', 'pending', '2026-11-13 01:30:00', '2026-11-13 01:30:00'),
(6, 9, 5, 5, '2026-11-13 14:00:00', 'ktp', 'penjemputan/identitas/ktp_deni.jpg', 'Jadwal imunisasi rutin di RS Hermina', 'disetujui', '2026-11-13 00:50:00', '2026-11-13 02:00:00'),
(7, 10, 6, 6, '2026-11-13 10:30:00', 'ktp', 'penjemputan/identitas/ktp_yanti.jpg', 'Anak sedikit demam ringan, minta dijemput siang', 'selesai', '2026-11-13 02:10:00', '2026-11-13 03:30:00'),
(8, 6, 7, 1, '2026-11-13 15:30:00', 'ktp', 'penjemputan/identitas/ktp_ratna.jpg', 'd', 'ditolak', '2026-11-13 02:45:00', '2026-11-12 21:28:32'),
(9, 7, 3, 4, '2026-11-12 16:30:00', 'ktp', 'penjemputan/identitas/ktp_hendra.jpg', 'Penjemputan sore biasa', 'selesai', '2026-11-12 03:00:00', '2026-11-12 09:35:00'),
(10, 9, 5, 5, '2026-11-12 12:00:00', 'ktp', NULL, 'Foto KTP tidak dilampirkan dan buram', 'ditolak', '2026-11-12 01:00:00', '2026-11-12 01:45:00'),
(11, 8, 4, 4, '2026-11-11 15:00:00', 'ktp', 'penjemputan/identitas/ktp_sari.jpg', 'Dibatalkan karena ibu jadi pulang cepat', 'dibatalkan', '2026-11-11 02:00:00', '2026-11-11 04:00:00'),
(12, 5, 7, 1, '2026-11-14 11:00:00', 'ktp', 'penjemputan/identitas/ktp_ratna.jpg', 'Besok Sabtu pulang setengah hari bersama Nenek', 'pending', '2026-11-13 03:15:00', '2026-11-13 03:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-11-11 17:00:00', '2026-11-11 17:00:00'),
(2, 'pengasuh', 'web', '2026-11-11 17:00:00', '2026-11-11 17:00:00'),
(3, 'orangtua', 'web', '2026-11-11 17:00:00', '2026-11-11 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('efemrNQ3k6GOrnJzCVuimgaNmvRweatwRqq29yHU', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIySmpGaWF1T3VlcnpFNGRBRENCdlNpWTB3bXE1NVdFcWtFdkljMGt6IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL3Blbmdhc3VoXC9wZW5qZW1wdXRhbiIsInJvdXRlIjoicGVuZ2FzdWgucGVuamVtcHV0YW4uaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjMsInBhc3N3b3JkX2hhc2hfd2ViIjoiYmQ2YzVlOTc1MDgzMjRlNjRhMmM0OWU0MDYzZjI5MzlhMzhmMTMwNDhiYTZiYTFiYzY4NDg5YmFjMWVlNjM5MCJ9', 1794544112),
('sHwCj58Q12Pcpw77GrkjSlLaPPu7PWbBgluHyDrN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJjTkJnS2Y3dW5Uelg0WmRJazdhNWRzVk9VSzVhUDBLa3NpVGV0VktxIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==', 1794937360);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', 'budi@example.com', NULL, '$2y$12$cc2SBqt9Z.87iNHm06vwkeGx/Kz1J3C261QBC2Ab42cGZkUzNFS2i', NULL, '2026-11-09 09:19:55', '2026-11-12 21:22:40'),
(2, 'Admin Utama', 'admin@titipanak.com', '2026-11-11 17:00:00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-11 17:00:00', '2026-11-11 17:00:00'),
(3, 'Siti Pengasuh', 'pengasuh@titipanak.com', '2026-11-11 17:00:00', '$2y$12$b7Smuk..WOs7QTJU23GQmeMyzW82qA3qq0v47d/hT1j6ZiGu8.6cK', NULL, '2026-11-11 17:00:00', '2026-11-12 21:20:13'),
(4, 'Rina Wijaya', 'rina@example.com', '2026-11-12 01:00:00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-12 01:00:00', '2026-11-12 01:00:00'),
(5, 'Dimas Pratama', 'dimas@example.com', '2026-11-12 01:00:00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-12 01:00:00', '2026-11-12 01:00:00'),
(6, 'Nadia Kusuma', 'nadia@example.com', '2026-11-12 01:00:00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-12 01:00:00', '2026-11-12 01:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak`
--
ALTER TABLE `anak`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_anak` (`kode_anak`),
  ADD KEY `fk_anak_user` (`user_id`),
  ADD KEY `fk_anak_kelas` (`kelas_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_laporan_harian_anak_tanggal` (`anak_id`,`tanggal`),
  ADD KEY `fk_laporan_harian_pengasuh` (`pengasuh_id`);

--
-- Indexes for table `laporan_harian_foto`
--
ALTER TABLE `laporan_harian_foto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_foto_laporan_harian` (`laporan_harian_id`);

--
-- Indexes for table `laporan_insiden`
--
ALTER TABLE `laporan_insiden`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_laporan_insiden_anak` (`anak_id`),
  ADD KEY `fk_laporan_insiden_pengasuh` (`pengasuh_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `penjemput`
--
ALTER TABLE `penjemput`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penjemputan`
--
ALTER TABLE `penjemputan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_penjemputan_anak` (`anak_id`),
  ADD KEY `fk_penjemputan_penjemput` (`penjemput_id`),
  ADD KEY `fk_penjemputan_user` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anak`
--
ALTER TABLE `anak`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `laporan_harian_foto`
--
ALTER TABLE `laporan_harian_foto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `laporan_insiden`
--
ALTER TABLE `laporan_insiden`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penjemput`
--
ALTER TABLE `penjemput`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penjemputan`
--
ALTER TABLE `penjemputan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anak`
--
ALTER TABLE `anak`
  ADD CONSTRAINT `fk_anak_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_anak_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  ADD CONSTRAINT `fk_laporan_harian_anak` FOREIGN KEY (`anak_id`) REFERENCES `anak` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_laporan_harian_pengasuh` FOREIGN KEY (`pengasuh_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_harian_foto`
--
ALTER TABLE `laporan_harian_foto`
  ADD CONSTRAINT `fk_foto_laporan_harian` FOREIGN KEY (`laporan_harian_id`) REFERENCES `laporan_harian` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_insiden`
--
ALTER TABLE `laporan_insiden`
  ADD CONSTRAINT `fk_laporan_insiden_anak` FOREIGN KEY (`anak_id`) REFERENCES `anak` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_laporan_insiden_pengasuh` FOREIGN KEY (`pengasuh_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penjemputan`
--
ALTER TABLE `penjemputan`
  ADD CONSTRAINT `fk_penjemputan_anak` FOREIGN KEY (`anak_id`) REFERENCES `anak` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_penjemputan_penjemput` FOREIGN KEY (`penjemput_id`) REFERENCES `penjemput` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_penjemputan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
