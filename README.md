# Facility Reservations API

REST API untuk Sistem Reservasi Fasilitas dan Ruangan Terpadu berbasis Laravel 11.

## Ringkasan

Backend ini menyediakan:
- autentikasi token via Laravel Sanctum
- role-based access control untuk `superadmin`, `admin_fakultas`, dan `peminjam`
- manajemen ruangan
- pengajuan reservasi
- persetujuan / penolakan reservasi
- generate dan download PDF surat izin penggunaan ruangan

## Tech Stack

- Laravel 11
- PHP 8.5
- SQLite
- Laravel Sanctum
- TCPDF untuk generate PDF
- Scribe untuk dokumentasi API

## Base URL

```text
http://localhost:8000/api/v1
```

## Akun Default Seeder

Semua akun memakai password `password123`.

- `superadmin@uin.ac.id` - role: `superadmin`
- `admin@uin.ac.id` - role: `admin_fakultas`
- `mahasiswa@uin.ac.id` - role: `peminjam`

## Alur API

### 1. Login

```http
POST /auth/login
```

Response login mengembalikan token Sanctum yang dipakai di header:

```http
Authorization: Bearer <token>
```

### 2. Reservasi

- `GET /reservations/available` untuk daftar ruangan aktif
- `POST /reservations` untuk membuat reservasi oleh role `peminjam`
- `GET /reservations/history` untuk histori reservasi user login

### 3. Approval

- `GET /approvals/pending` untuk daftar reservasi pending
- `PUT /approvals/{reservation}/approve` untuk approve dan generate PDF
- `PUT /approvals/{reservation}/reject` untuk menolak reservasi

Saat approve, body JSON yang wajib dikirim hanya:

```json
{
	"nama_penyetuju": "Dr. Nama Penyetuju",
	"jabatan_penyetuju": "Wakil Rektor Bidang Akademik"
}
```

### 4. Download PDF

- `GET /reservations/{reservation}/letter/pdf`

Endpoint ini hanya bisa diakses oleh pemilik reservasi atau admin yang login.

## File Penting untuk FE

### Template PDF

- [resources/views/letters/permission-letter.blade.php](resources/views/letters/permission-letter.blade.php)

Template ini adalah sumber tampilan surat izin. Jika desain PDF berubah, edit file ini.

### Logo PDF

- [public/images/logo/uin-logo.png](public/images/logo/uin-logo.png)

Gunakan PNG atau JPG. WEBP tidak direkomendasikan untuk TCPDF.

## Testing

File [test.http](test.http) berisi request siap pakai untuk REST Client di VS Code.

Urutan testing yang umum:
1. login
2. create reservation sebagai `peminjam`
3. approve sebagai `admin_fakultas`
4. download PDF

## Generate PDF Ulang Saat Template Berubah

Jika template Blade diubah, PDF baru akan mengikuti template baru saat endpoint approve dijalankan ulang.

Cara cepat untuk testing:
- ubah status reservation jadi `pending`
- kirim ulang request `PUT /approvals/{reservation}/approve`

## Lokasi Output PDF

File PDF hasil generate disimpan di:

```text
storage/app/letters/
```

## Konfigurasi Lokal

File environment lokal yang umum dipakai:
- `.env`
- `database/database.sqlite`

Keduanya tidak perlu dibagikan ke pihak frontend.

## Catatan Integrasi FE

- Semua request protected harus memakai header `Authorization: Bearer <token>`.
- Frontend cukup konsumsi endpoint JSON yang sudah tersedia.
- PDF dapat diunduh langsung dari endpoint download setelah approval selesai.
- Jika FE butuh contoh response atau Swagger/OpenAPI, gunakan output Scribe di `/docs`.

## Quick Start

```bash
php artisan serve --port=8000
php artisan migrate --seed
```

Lalu buka:

```text
http://localhost:8000/docs
```
