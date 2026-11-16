# Panduan Menyalin Fitur Laravel Secara Manual

Panduan ini menjelaskan file yang perlu dipindahkan dari branch rumah ke project sekolah dan urutan aman untuk menjalankannya tanpa git clone/pull.

## Dasar perbandingan Git

CRUD kategori dan alat sudah ada pada commit awal **59171ad** (first commit). Saya membandingkannya dengan HEAD saat ini **aaedd6e** (feat: registrasi).

Commit setelah CRUD kategori/alat:

1. **ec40e0f** — feat: crud
2. **d2ee50a** — feat: smt in readme
3. **c3c4a7e** — feat: crud,detail dan log aktivitas
4. **d452353** — feat: dashboard
5. **38bd607** — feat: login, perubahan di peminjaman alat
6. **aaedd6e** — feat: registrasi

Perbandingan **59171ad..aaedd6e** berisi **55 file berubah**: 42 file baru dan 13 file dimodifikasi. Jumlah diff Git: 2.509 penambahan dan 156 penghapusan. Diff juga mencakup README serta perubahan tampilan pada beberapa halaman lama. composer.json, composer.lock, config/auth.php, dan model Alat tidak berubah. Tidak ada Breeze yang ditambahkan; autentikasi memakai session guard Laravel.

> File yang ditandai “dimodifikasi” sebaiknya diganti seluruh isinya dengan versi dari commit HEAD, setelah menyimpan salinan perubahan lokal sekolah. Jangan menambahkan isinya di akhir file karena route/class ganda dapat menyebabkan error.

## 1. Daftar file dari diff

### Migration baru

Salin ke database/migrations/:

- 2026_11_16_130806_create_peminjamans_table.php
- 2026_11_16_131329_create_detail_peminjamans_table.php
- 2026_11_16_131335_create_pengembalians_table.php
- 2026_11_16_140000_rename_kodisi_awal_on_detail_peminjamans_table.php
- 2026_11_16_150000_create_log_aktivitas_table.php
- 2026_11_17_080001_update_role_and_loan_status_columns.php
- 2026_11_17_080002_add_status_to_pengembalians_table.php

### Migration lama yang dimodifikasi

- database/migrations/0001_01_01_000000_create_users_table.php — role awal kini memakai admin, petugas, peminjam.
Migration peminjaman tercantum sebagai file baru dan sudah mendefinisikan status pending, dipinjam, ditolak, serta dikembalikan. Pastikan hanya ada satu file dengan nama tersebut dan gunakan isi terbaru.

### Model baru dan model yang harus disesuaikan

Model baru:

- app/Models/DetailPeminjaman.php
- app/Models/LogAktivitas.php
- app/Models/Peminjaman.php
- app/Models/Pengembalian.php

Model lama yang dimodifikasi:

- app/Models/User.php

Model Kategori dan Alat sudah ada pada project awal; model Alat tidak berubah pada diff ini.

### Controller baru

- app/Http/Controllers/Auth/LoginController.php
- app/Http/Controllers/Auth/RegisterController.php
- app/Http/Controllers/DashboardController.php
- app/Http/Controllers/DetailPeminjamanController.php
- app/Http/Controllers/LaporanController.php
- app/Http/Controllers/LogAktivitasController.php
- app/Http/Controllers/PeminjamanController.php
- app/Http/Controllers/PengembalianController.php
- app/Http/Controllers/UserController.php

Middleware baru:

- app/Http/Middleware/RoleMiddleware.php

Controller lama yang dimodifikasi:

- app/Http/Controllers/AlatController.php — menambah katalog peminjam dan penyesuaian akses.

KategoriController.php sudah tersedia dari project awal dan tidak berubah di diff ini.

### View baru

- resources/views/auth/login.blade.php
- resources/views/auth/register.blade.php
- resources/views/dashboard.blade.php
- resources/views/pages/alats/catalog.blade.php
- resources/views/pages/detail-peminjamans/index.blade.php
- resources/views/pages/laporan/index.blade.php
- resources/views/pages/log-aktivitas/index.blade.php
- resources/views/pages/peminjamans/create.blade.php
- resources/views/pages/peminjamans/edit.blade.php
- resources/views/pages/peminjamans/index.blade.php
- resources/views/pages/peminjamans/mine.blade.php
- resources/views/pages/peminjamans/request.blade.php
- resources/views/pages/pengembalians/create.blade.php
- resources/views/pages/pengembalians/edit.blade.php
- resources/views/pages/pengembalians/index.blade.php
- resources/views/pages/pengembalians/mine.blade.php
- resources/views/pages/users/create.blade.php
- resources/views/pages/users/edit.blade.php
- resources/views/pages/users/index.blade.php

### View lama yang dimodifikasi

- resources/views/layouts/app.blade.php — menampilkan identitas role dan tombol logout.
- resources/views/layouts/sidemenu.blade.php — menu sidebar sesuai role.
- resources/views/pages/alats/create.blade.php
- resources/views/pages/alats/edit.blade.php
- resources/views/pages/alats/index.blade.php
- resources/views/pages/kategoris/create.blade.php
- resources/views/pages/kategoris/edit.blade.php

Perubahan pada halaman kategori/alat lama berupa penyesuaian tampilan/role; migration dan CRUD kategori/alat awal tidak perlu dibuat ulang. resources/views/welcome.blade.php tidak berubah, tetapi route / sekarang menuju dashboard.

### File konfigurasi dan data yang dimodifikasi

- routes/web.php — route guest, auth, role, dashboard, modul transaksi, dan katalog.
- bootstrap/app.php — alias middleware role dan redirect pengguna yang sudah login.
- database/seeders/DatabaseSeeder.php — akun demo tiga role.
- database/factories/UserFactory.php — disesuaikan dengan kolom users.
- README.md — catatan proyek.

Daftar di atas adalah seluruh path pada perbandingan Git setelah commit awal. Jika file sekolah berbeda karena ada perubahan lokal, bandingkan dulu sebelum mengganti.

## 2. Urutan penyalinan dari database ke tampilan

### Tahap 1: Database migrations

Pastikan migration dasar sekolah sudah tersedia dan pernah dijalankan:

- 0001_01_01_000000_create_users_table.php
- 2026_11_16_023425_kategori.php
- 2026_11_16_033836_alat.php

Tabel alat memiliki FK ke kategori. Fitur lanjutan mengasumsikan tabel users dan alats tersedia.

Salin migration baru dalam urutan timestamp berikut:

1. **2026_11_16_130806_create_peminjamans_table.php**  
   Membuat peminjaman dengan FK user_id ke users.id.

2. **2026_11_16_131329_create_detail_peminjamans_table.php**  
   Membuat rincian dengan FK ke peminjamans.id dan alats.id.

3. **2026_11_16_131335_create_pengembalians_table.php**  
   Membuat pengembalian dengan FK ke peminjaman dan user.

4. **2026_11_16_140000_rename_kodisi_awal_on_detail_peminjamans_table.php**  
   Memperbaiki nama kolom lama kodisi_awal menjadi kondisi_awal. Migration create terkini sudah memakai ejaan benar; migration perbaikan melewati perubahan jika kolom typo tidak ada.

5. **2026_11_16_150000_create_log_aktivitas_table.php**  
   Membuat log aktivitas dengan FK nullable ke users.

6. **2026_11_17_080001_update_role_and_loan_status_columns.php**  
   Memetakan role lama user ke peminjam dan mengubah kolom role/status agar menerima nilai baru.

7. **2026_11_17_080002_add_status_to_pengembalians_table.php**  
   Menambahkan status verifikasi pengembalian.

Laravel mengurutkan migration berdasarkan timestamp nama file; jangan mengganti timestamp atau nama file. Tabel peminjaman harus dibuat sebelum detail dan pengembalian karena keduanya mereferensikannya.

#### Cek skema lama sebelum migrate

- Migration users lama yang sudah tercatat tidak dijalankan ulang hanya karena filenya diganti. Migration baru 080001 menangani role user yang lama.
- Jika tabel users sekolah masih memakai kolom email bawaan Laravel dan belum punya username/nama_lengkap, migration transisi di atas tidak menambahkan kolom itu. Samakan skema users terlebih dahulu dengan migration khusus yang sesuai database sekolah.
- Nama kolom tanggal saat ini adalah tanggal_kembali_rencana, bukan rencana_kembali. Migration, model, controller, dan view harus konsisten.
- Migration awal kategori dan alat jangan disalin sebagai migration baru jika sudah ada di sekolah. Jika dijalankan ulang, Laravel dapat memberi error “table already exists”.
- Sebelum migrate, jalankan php artisan migrate:status untuk mengetahui migration yang sudah tercatat.

### Tahap 2: Models

Salin model setelah tabel dasarnya tersedia.

| Model | Relasi | Field penting di fillable |
|---|---|---|
| Peminjaman | belongsTo(User), hasMany(DetailPeminjaman), hasMany(Pengembalian) | user_id, tanggal_pinjam, tanggal_kembali_rencana, status, keperluan |
| DetailPeminjaman | belongsTo(Peminjaman), belongsTo(Alat) | peminjaman_id, alat_id, jumlah, kondisi_awal |
| Pengembalian | belongsTo(Peminjaman), belongsTo(User) | peminjaman_id, user_id, tanggal_kembali_aktual, kondisi_kembali, denda, keterangan, status |
| LogAktivitas | belongsTo(User) | user_id, aktivitas, modul, created_at |
| User | model autentikasi Laravel | nama_lengkap, username, password, role |

LogAktivitas mengatur timestamp Eloquent secara manual karena tabel hanya memiliki created_at. Model User harus extends Authenticatable, menyembunyikan password, dan memakai cast password hashed.

Relasi alat kategori yang sudah ada adalah Alat belongsTo Kategori. Pertahankan model kategori/alat sekolah jika relasi dan nama kolomnya sudah cocok.

### Tahap 3: Controllers

Salin controller setelah model tersedia:

- Auth: LoginController dan RegisterController.
- Dashboard: DashboardController.
- Read-only: DetailPeminjamanController dan LogAktivitasController.
- Operasional: PeminjamanController, PengembalianController, dan LaporanController.
- User admin: UserController.
- Alat: ganti AlatController agar menyediakan katalog serta penyesuaian akses.

Validasi request dilakukan langsung melalui $request->validate(...); tidak ada FormRequest terpisah. Registrasi publik selalu menetapkan role peminjam di server. Peminjam membuat pengajuan dengan user_id dari sesi login dan status pending. Petugas menyetujui/menolak; stok dipotong saat disetujui dan dikembalikan saat pengembalian diverifikasi.

### Tahap 4: Routes

Ganti routes/web.php dengan versi HEAD agar route tidak tertinggal atau terduplikasi.

- Guest: GET/POST login dan register.
- auth: semua route aplikasi dan logout.
- role:admin: user, kategori, hapus alat, CRUD transaksi, log aktivitas.
- role:admin,petugas: dashboard, alat, keputusan peminjaman, verifikasi pengembalian, detail, laporan.
- role:peminjam: katalog, pengajuan, peminjaman sendiri, pengembalian sendiri.

Route petugas tidak memiliki DELETE alat, dan route peminjam tidak memakai route CRUD admin. Middleware mencegah akses melalui URL langsung.

### Tahap 5: Views

Salin semua view baru pada daftar Tahap 1, lalu ganti dua layout:

- layouts/app.blade.php: navbar, nama/role, logout.
- layouts/sidemenu.blade.php: menu per role.

Salin semua view Peminjaman, Pengembalian, User, Detail, Log, Laporan, Login, Registrasi, Dashboard, serta katalog alat. Pastikan subfolder dan ejaan path sama persis. Halaman login/register tidak memakai layout admin. Katalog alat berbeda dari halaman CRUD alat karena peminjam hanya melihat stok dan mengajukan pinjaman.

### Tahap 6: Konfigurasi dan seeder

- app/Http/Middleware/RoleMiddleware.php memeriksa role pengguna.
- bootstrap/app.php mendaftarkan alias role dan redirect user yang sudah login.
- DatabaseSeeder.php membuat akun admin, petugas, dan peminjam dengan password hash.
- UserFactory.php memakai skema users yang benar.
- config/auth.php bawaan sudah sesuai dengan session guard dan provider Eloquent. Composer tidak berubah; tidak perlu install Breeze atau menjalankan npm.

Akun demo dari seeder:

| Role | Username | Password |
|---|---|---|
| Admin | admin | admin12345 |
| Petugas | petugas | petugas12345 |
| Peminjam | peminjam | peminjam12345 |

## 3. Perintah Artisan setelah penyalinan

Jalankan dari root project sekolah:

1. Bersihkan cache lama: **php artisan optimize:clear**
2. Periksa status migration: **php artisan migrate:status**
3. Terapkan migration yang belum pernah dijalankan: **php artisan migrate**
4. Buat/perbarui akun demo: **php artisan db:seed**
5. Periksa route: **php artisan route:list --except-vendor**
6. Jika class baru tidak terdeteksi, jalankan **composer dump-autoload**.

Pastikan .env menunjuk ke database sekolah yang benar sebelum langkah migrate dan seed. Jangan jalankan **php artisan migrate:fresh** pada database berisi data karena perintah tersebut menghapus tabel dan isinya.