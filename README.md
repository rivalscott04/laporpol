# Laporan Kegiatan Polmas Ditres PPA & PPO Polda NTB

Aplikasi web untuk **mencatat dan mengelola laporan kegiatan** Polmas (Polisi Masyarakat) Ditres PPA & PPO Polda NTB. Petugas bisa mengunggah bukti kegiatan dari lapangan; pengelola bisa melihat, mencari, merangkum, dan mengekspor data laporan.

---

## Apa yang bisa dilakukan aplikasi ini?

| Fitur | Penjelasan singkat |
|-------|-------------------|
| **Buat laporan kegiatan** | Catat tanggal, lokasi, koordinat, foto, dan lampiran PDF |
| **Lihat & ubah laporan** | Daftar laporan bisa dicari dan difilter |
| **Rekap harian / mingguan / bulanan** | Ringkasan jumlah laporan per periode |
| **Ekspor Excel & PDF** | Unduh data laporan untuk arsip atau laporan resmi |
| **Kelola pengguna** | Tambah dan atur akun petugas (khusus pengelola) |
| **Catatan aktivitas** | Jejak siapa mengubah data dan kapan (khusus pengawas) |

---

## Siapa yang pakai aplikasi ini?

Aplikasi punya tiga tingkat akses:

| Peran | Siapa? | Bisa apa? |
|-------|--------|-----------|
| **Petugas** | Anggota yang melaporkan kegiatan lapangan | Buat, lihat, ubah, dan hapus laporan sendiri; ubah profil |
| **Pengelola** | Admin satuan / bagian | Semua akses petugas, plus kelola semua laporan, kelola pengguna, rekap, ekspor |
| **Pengawas Utama** | Super admin | Semua akses pengelola, plus pengaturan unggahan, impor pengguna, dan log aktivitas |

---

## Cara masuk

1. Buka alamat aplikasi di browser (Chrome, Firefox, atau Edge disarankan).
2. Anda akan diarahkan ke halaman **Masuk**.
3. Isi **Email atau NRP/NIP** dan **Kata sandi**.
4. Klik **Masuk**.

> **Catatan:** NRP/NIP adalah nomor identitas petugas yang terdaftar di sistem (contoh: `96000003`). Jika lupa kata sandi, hubungi pengelola satuan.

Alamat halaman masuk biasanya: **`/admin/login`**  
Contoh: `https://nama-server-anda.com/admin/login`

---

## Panduan singkat untuk petugas

### Membuat laporan baru

1. Setelah masuk, buka menu **Laporan** → **Buat Laporan** (atau tombol serupa).
2. Isi **Tanggal** kegiatan.
3. Isi **Nama Lokasi** — tulis tempat yang mudah dikenali, misalnya *Monas, Jakarta Pusat* atau *Kantor Polres Mataram*.
4. Isi **Garis Lintang** dan **Garis Bujur** (koordinat GPS):
   - Salin dari Google Maps atau aplikasi peta di ponsel.
   - Format angka desimal, bukan derajat menit detik.
   - Contoh lintang: `-8.5833333`, bujur: `116.1166667`.
5. Tulis **Catatan** singkat tentang kegiatan (opsional tapi disarankan).
6. **Unggah Foto** — gunakan foto yang sudah diberi **cap waktu dan lokasi** dari aplikasi kamera ponsel Anda.
7. **Unggah Lampiran PDF** — dokumen pendukung wajib format PDF.
8. Simpan laporan.

### Mengubah atau menghapus laporan

- Buka daftar **Laporan**, pilih laporan yang ingin diubah.
- Petugas hanya bisa mengubah laporan milik sendiri.
- Penghapusan bersifat arsip (data tidak hilang permanen dari sistem).

### Mengubah profil

- Klik menu profil di pojok kanan atas → **Edit Profil**.
- Anda bisa mengubah nama dan kata sandi.

---

## Panduan singkat untuk pengelola

Selain semua fitur petugas, pengelola dapat:

- **Kelola Pengguna** — tambah, ubah, atau nonaktifkan akun petugas.
- **Kelola semua laporan** — lihat dan ubah laporan dari semua petugas.
- **Rekap Laporan** — pilih periode harian, mingguan, atau bulanan; lihat ringkasan dan unduh ekspor.
- **Ekspor** — unduh data laporan ke Excel atau PDF.

---

## Hal yang perlu diperhatikan saat mengunggah file

- **Foto:** format gambar (JPG/PNG), ukuran maksimal bisa diatur pengelola (standar sekitar 1 MB).
- **Lampiran:** wajib **PDF**, ukuran maksimal sekitar 1 MB.
- Pastikan **izin lokasi** di ponsel aktif saat mengambil foto bukti.
- Koordinat harus **akurat** — jangan isi angka sembarangan.

---

## Pertanyaan yang sering ditanyakan

**Saya tidak bisa masuk. Apa yang harus dilakukan?**  
Pastikan NRP/NIP atau email benar, caps lock tidak aktif, dan kata sandi tepat. Jika masih gagal, minta pengelola mereset kata sandi Anda.

**Browser mana yang disarankan?**  
Chrome atau Firefox versi terbaru. Hindari browser sangat lama di ponsel Android lama.

**Apakah bisa dipakai di HP?**  
Ya. Buka alamat yang sama di browser ponsel; tampilan menyesuaikan layar kecil.

**Foto wajib ada cap waktu?**  
Ya. Gunakan fitur cap waktu + lokasi di aplikasi kamera ponsel sebelum mengunggah.

**Siapa yang bisa melihat laporan saya?**  
Anda sendiri, pengelola, dan pengawas utama — sesuai hak akses masing-masing peran.

---

## Untuk tim IT / pengembang

Bagian ini untuk yang menginstal atau merawat server aplikasi.

### Persyaratan

- PHP 8.2+
- Composer
- Node.js & npm (untuk aset front-end)
- Database: SQLite (development) atau MySQL/PostgreSQL (production)

### Instalasi cepat

```bash
composer setup
```

Perintah di atas akan menginstal dependensi, membuat file `.env`, menjalankan migrasi database, dan membangun aset.

Atau langkah manual:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run build
```

### Menjalankan di komputer lokal

```bash
composer dev
```

Atau:

```bash
php artisan serve
```

Buka `http://localhost:8000` — akan diarahkan ke halaman masuk.

### Akun contoh (setelah `db:seed`)

| Peran | NRP/NIP / Email | Kata sandi default |
|-------|-----------------|-------------------|
| Pengawas Utama | `22071998` atau `superadmin@laporanpol.test` | `password` |
| Pengelola | `96000002` atau `admin@laporanpol.test` | `password` |
| Petugas | `96000003` atau `user@laporanpol.test` | `password` |

Kata sandi seed bisa diubah lewat variabel `SEED_USER_PASSWORD` di file `.env`.

> **Penting:** Ganti semua kata sandi default sebelum dipakai di lingkungan produksi.

### Pengaturan nama & logo

Di file `.env`:

```env
APP_BRAND_SHORT="Polmas Ditres PPA & PPO NTB"
APP_BRAND_FULL="Laporan Kegiatan Polmas Ditres PPA & PPO Polda NTB"
APP_BRAND_LOGO=images/logo.png
```

### Menjalankan tes

```bash
composer test
```

---

## Lisensi

Proyek ini menggunakan [Laravel](https://laravel.com) (MIT License) beserta paket pendukungnya.
