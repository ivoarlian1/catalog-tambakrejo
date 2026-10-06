# E-Catalog Kelurahan Tambakrejo

Aplikasi E-Catalog Kelurahan Tambakrejo merupakan platform digital dan direktori informasi untuk menampilkan UMKM, fasilitas pendidikan, fasilitas kesehatan, dan fasilitas umum di wilayah Kelurahan Tambakrejo.

Aplikasi ini adalah direktori digital, bukan marketplace. Laravel menyimpan profil katalog, kontak, koordinat, dan URL detail; visualisasi lokasi dikelola oleh ArcGIS sebagai sistem terpisah.

## Features

- **Katalog Digital**: kategori awal UMKM, Pendidikan, Kesehatan, Fasilitas Umum; Superadmin dapat menambah kategori tanpa mengubah kode.
- **Katalog publik**: pencarian kategori/subkategori, filter, pagination, detail, kontak, dan produk UMKM.
- **Superadmin**: mengelola kategori, katalog, status publikasi, akun Catalog Admin, dan seluruh produk.
- **Catalog Admin**: mengelola satu profil katalog, foto, dan produk; ownership diperiksa server-side.
- **Subkategori bebas**: dapat diketik langsung, disimpan sebagai saran kategori untuk digunakan kembali.
- **Foto katalog**: cover tunggal, foto lokasi, dan galeri; foto `catalogs.photo` existing tetap menjadi fallback.
- **QR Code**: tampilan SVG dan unduhan JPG/JPEG mengarah ke URL detail Laravel dengan slug stabil.
- **Koordinat**: latitude dan longitude divalidasi dan ditampilkan sebagai teks, tanpa peta di Laravel.
- **Responsive UI**: Blade dan Tailwind CSS dengan navigasi keyboard dan form berlabel.

## Tech Stack

- **Backend**: Laravel 13, PHP 8.3+
- **Frontend**: Blade, Tailwind CSS 4, Vite
- **Database**: SQLite (default) atau MySQL/MariaDB
- **Authentication**: Laravel session authentication
- **QR**: Simple Software IO QR Code

## Requirements

- PHP 8.3 atau lebih baru dengan ekstensi yang dibutuhkan Laravel, PDO SQLite atau PDO MySQL, fileinfo, dan GD.
- Composer 2, Node.js/npm, dan database SQLite atau MySQL/MariaDB.

## Installation

```bash
composer install
npm install
```

Salin `.env.example` menjadi `.env` (di PowerShell gunakan `Copy-Item .env.example .env`), buat file SQLite bila diperlukan, lalu siapkan key:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
```

Atur `APP_URL` dan `DB_*` pada `.env`. Template memakai SQLite. Untuk MySQL, atur `DB_CONNECTION=mysql`, host, port, nama database, dan kredensial lokal Anda.

## Database Setup

Jalankan migration dan sample development data:

```bash
php artisan migrate --seed
```

`php artisan migrate:fresh --seed` menghapus seluruh tabel terlebih dahulu; jalankan hanya pada database development/test yang boleh dikosongkan.

Seeder membuat satu Superadmin, delapan katalog sample, beberapa produk UMKM, serta Catalog Admin untuk setiap katalog. Kredensial sample: `admin@example.test` / `password`; Catalog Admin sample menggunakan `siti@example.test` / `password`. Semua password tersebut hanya untuk development dan harus diganti sebelum penggunaan nyata.

## Authentication and Roles

- `/superadmin` membuka login Superadmin; dashboard ada di `/superadmin/dashboard`.
- `/catalog-admin` membuka login Catalog Admin; dashboard ada di `/catalog-admin/dashboard`.
- Tidak ada public registration. Superadmin membuat akun Catalog Admin dan menetapkan satu katalog.
- Akun nonaktif ditolak, login dibatasi, password di-hash, dan logout menginvalidasi session.
- Authorization memakai middleware/policy; Catalog Admin tidak dapat mengubah `catalog_id` atau mengakses katalog pengguna lain.

## Catalog and Product Management

Superadmin dapat membuat, mengedit, menghapus, publish/unpublish katalog, serta mengelola akun admin dan semua produk. Slug dibuat saat katalog dibuat dan dikunci saat edit agar URL/QR tetap stabil. Catalog Admin hanya mengedit profil dan produk untuk katalog miliknya. Produk tersedia untuk kategori UMKM.

Kategori dikelola di `/superadmin/categories`. Nama dan status kategori dapat diubah, tetapi slug kategori dipertahankan agar URL existing tidak berubah. Kategori yang sudah dipakai katalog tidak dapat dihapus; nonaktifkan untuk mencegah penggunaan baru. Subkategori menerima teks bebas dan tersimpan sebagai saran reusable.

Foto dikelola dari halaman edit profil katalog. Unggahan menerima hingga 10 gambar per batch (maksimum 2 MB per gambar), dapat ditandai sebagai cover, lokasi, atau gallery. Hanya satu cover aktif; saat diganti, cover sebelumnya menjadi foto tambahan. Migration menyalin path foto lama ke tabel `catalog_photos` tanpa menghapus path/file asal.

Katalog publik hanya menampilkan status `published`. Koordinat bersifat nullable dan divalidasi pada rentang latitude -90 sampai 90 serta longitude -180 sampai 180.

## QR Code and ArcGIS

QR Code dapat diunduh sebagai JPEG melalui encoder QR yang ada dan PHP GD; SVG lama juga tersedia. QR hanya berisi URL detail Laravel (`/katalog/{type}/{slug}`), tidak berisi koordinat atau URL ArcGIS. Lihat [docs/arcgis-workflow.md](docs/arcgis-workflow.md) untuk alur pemindahan data secara manual ke project ArcGIS eksternal. Aplikasi Laravel tidak memuat peta, SDK, embed, API, maupun integrasi runtime ArcGIS.

## File Storage

Foto divalidasi sebagai JPEG, PNG, atau WebP dengan batas 2 MB per file dan disimpan dengan nama acak berbasis UUID. Di lokal, `MEDIA_DISK=public` memakai Laravel Filesystem; jalankan `php artisan storage:link` agar URL `/storage/...` bisa dibaca oleh web server. Di Vercel, gunakan Supabase Storage agar file tetap tersedia antar-invocation:

1. Buat bucket `catalog-images` di Supabase Storage dan tandai sebagai **public**. Isi bucket dapat dilihat siapa pun yang memiliki URL.
2. Dari Supabase Project Settings → API Keys, ambil URL project dan secret `service_role` legacy key. Jangan gunakan anon key, jangan beri awalan `VITE_`, dan jangan commit key ini.
3. Tambahkan environment variables berikut di Vercel untuk environment Production:
   - `MEDIA_DISK=supabase`
   - `SUPABASE_URL=https://<project-ref>.supabase.co`
   - `SUPABASE_SERVICE_ROLE_KEY=<service_role-secret>`
   - `SUPABASE_STORAGE_BUCKET=catalog-images`
4. Redeploy Vercel. Upload foto baru melalui halaman admin; URL gambar akan disajikan dari bucket Supabase.

Foto lama yang tersimpan di filesystem sementara Vercel tidak dapat dipulihkan dari database saja. Unggah ulang file aslinya setelah konfigurasi aktif. Gunakan gambar fallback saat path database tidak menunjuk ke file yang tersedia.

Navbar memakai simbol placeholder mandiri di `public/images/logo/ecatalog-mark.svg`; simbol tersebut bukan logo resmi Kelurahan dan dapat diganti dengan file logo resmi kelak. Footer publik menampilkan `© KKN GIAT 17 UNNES`.

## Development Server

```bash
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Untuk hot reload aset saat pengembangan, jalankan `npm run dev` pada terminal terpisah.

## Testing

```bash
php artisan test
npm run build
php artisan route:list
```

Tes memakai SQLite in-memory dan mencakup kategori dinamis, custom subkategori, slug/detail/QR JPG dan SVG, validasi koordinat, upload/fallback/gallery foto, ownership katalog/foto/produk, akses role, dan render layar admin.

## Security and Production

CSRF aktif pada form, output Blade di-escape, query memakai Eloquent binding, file upload dibatasi, login di-rate-limit, dan akses katalog/produk diperiksa di server. Sebelum production, buat kredensial admin sendiri, gunakan HTTPS, `APP_DEBUG=false`, secret environment variable, backup database, serta storage persisten. Jangan commit `.env` atau credential.

Build aset dan migrasikan database pada rilis:

```bash
npm run build
php artisan migrate --force
```

Repository ini menyertakan konfigurasi Vercel untuk runtime PHP dan file statis `public/**`. Jalankan `npm run build` dan sertakan hasil `public/build` saat deploy agar CSS/JS tersedia. Siapkan database eksternal yang persisten dan object storage/persistent storage untuk upload karena filesystem serverless bersifat ephemeral. Atur `APP_URL` di Vercel ke URL HTTPS production (contoh: `https://catalogtambakrejo.vercel.app`). Aplikasi mempercayai header protokol dari proxy hanya saat berjalan melalui entrypoint Vercel, sehingga URL aset dan tautan dibuat dengan skema HTTPS. Uji setiap deployment; hosting PHP tradisional atau Laravel Cloud juga dapat dipertimbangkan.
