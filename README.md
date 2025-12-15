<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# 🏢 Meeting Room Booking API

Sebuah **Backend REST API** untuk sistem pemesanan ruang meeting, dibangun menggunakan **Laravel 12** dan **PHP 8.4**.
Sistem ini dirancang dengan fokus pada **aturan bisnis yang jelas**, **struktur kode yang rapi**, dan **perilaku booking yang realistis** seperti di dunia nyata.

---

## 🧰 Teknologi yang Digunakan

* **PHP**: 8.4
* **Laravel Framework**: 12
* **Database**: MySQL / MariaDB
* **Authentication**: JWT (JSON Web Token)

---

## 🎯 Tujuan Sistem

Sistem ini memungkinkan:

* User melakukan **registrasi dan login**
* User melihat daftar ruang meeting
* User melakukan booking ruang meeting berdasarkan jam
* Admin mengelola data ruang meeting
* Sistem mencegah **double booking** dan konflik waktu

---

## 🧱 Gambaran Arsitektur (Clean Architecture)

Struktur folder utama:

```
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Services/
├── Repositories/
├── Helpers/
database/
├── migrations/
├── seeders/
├── factories/
tests/
├── Feature/
```

### Penjelasan singkat:

* **Controller**
  Menerima request HTTP dan mengembalikan response JSON
  (tidak berisi logika bisnis berat)

* **Service**
  Tempat aturan bisnis (business rules) ditulis
  Contoh: validasi waktu booking, pengecekan konflik

* **Repository**
  Bertugas berinteraksi dengan database
  Supaya query tidak bercampur dengan logika bisnis

* **Helper**
  Digunakan untuk menyamakan format response API


* **Note ->**
 Pada implementasi saat ini, sebagian besar aturan bisnis
 (durasi booking, konflik waktu, validasi jam)
 ditempatkan langsung di Controller              
 Pendekatan ini dipilih untuk menjaga kesederhanaan
 mengingat scope aplikasi masih kecil dan terkontrol.
---

## 🔐 Authentication & Authorization

* Sistem menggunakan **JWT (JSON Web Token)**
* Token dikirim melalui header:

  ```
  Authorization: Bearer {token}
  ```
* Saat register:

  * Jika role **tidak dikirim**, otomatis menjadi `user`
  * Jika dikirim `admin`, maka user menjadi admin

### Hak Akses:

* **User biasa**

  * Melihat room
  * Melakukan booking
  * Melihat booking miliknya sendiri

* **Admin**

  * Menambah, mengubah, dan menghapus room

---

## 📅 Aturan Booking (Business Rules)

Aturan berikut dibuat agar sistem sesuai dengan logika penggunaan ruang meeting di dunia nyata.

### 1️⃣ Booking Berbasis Jam

* Waktu booking harus **jam bulat** (`HH:00`)
* Minimal durasi booking adalah **1 jam**

### 2️⃣ Booking Hanya Dalam 1 Hari

* Tidak boleh booking lintas hari
* Contoh:

  * `2025-12-16 10:00 → 2025-12-17 10:00` ❌

### 3️⃣ Jam Operasional

* Booking hanya boleh dilakukan antara:

  ```
  08:00 – 18:00
  ```

### 4️⃣ Booking yang Sedang Berjalan Tetap Boleh

* Booking masih dianggap valid selama **end time belum lewat**
* Contoh:

  * Sekarang jam `12:23`
  * Booking `12:00 – 13:00` → ✅ boleh
* Konsep ini mirip seperti membeli tiket bioskop yang sedang tayang

### 5️⃣ Pencegahan Double Booking

* Dua booking dianggap konflik jika:

  ```
  booking_lama.start < booking_baru.end
  DAN
  booking_lama.end > booking_baru.start
  ```
* Booking sambung (`08:00–12:00` lalu `12:00–13:00`) **diperbolehkan**

---

## 🔄 Penanganan Booking Bersamaan (Concurrency)

Untuk mencegah dua user booking ruangan yang sama di waktu yang hampir bersamaan:

* Sistem menggunakan **database transaction**
* Menggunakan `lockForUpdate`
* Hanya satu booking yang akan berhasil disimpan

---

## 📡 Endpoint Utama

### Authentication

* `POST /api/auth/register`
* `POST /api/auth/login`

### Rooms

* `GET /api/rooms`
* `POST /api/rooms` *(admin only)*
* `PUT /api/rooms/{id}` *(admin only)*
* `DELETE /api/rooms/{id}` *(admin only)*

### Bookings

* `POST /api/bookings`
* `GET /api/bookings/my`
* `GET /api/rooms/{id}/availability?date=YYYY-MM-DD`

---

## 📤 Format Response API

Semua response API dibuat konsisten menggunakan helper.

### Response Berhasil

```json
{
  "message": "Success",
  "data": { ... }
}
```

### Response Gagal

```json
{
  "Error": "BOOKING_CONFLICT",
  "Massage": "Room already booked for this time slot"
}
```

---

## 🗄️ Cara Menjalankan Database (Migrasi & Seeder)

### 1️⃣ Pastikan database sudah dibuat

Contoh:

```
meeting_room_db
```

### 2️⃣ Atur koneksi database di `.env`

```env
DB_CONNECTION=mysql
DB_DATABASE=meeting_room_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3️⃣ Jalankan migrasi database

Perintah ini akan membuat semua tabel:

```bash
php artisan migrate
```

### 4️⃣ Jalankan seeder (data awal)

Seeder akan mengisi data contoh seperti room:

```bash
php artisan db:seed
```

Atau sekaligus:

```bash
php artisan migrate --seed
```

---

## 🧪 Unit Test / Feature Test

Project ini menggunakan **Feature Test** untuk memastikan:

* Booking tidak overlap
* Booking sambung diperbolehkan
* Booking yang sedang berjalan valid
* User hanya bisa melihat booking miliknya sendiri
* Authentication bekerja dengan JWT asli

### Menjalankan test:

```bash
php artisan test
```

Semua test berjalan dengan:

* Database fresh (migrasi ulang)
* Data dibuat lewat factory
* Tanpa bergantung data manual

---

## 🧠 Filosofi Desain

* Sistem mengikuti cara manusia berpikir, bukan hanya teknis
* Aturan booking dibuat eksplisit agar tidak ambigu
* Fokus pada kejelasan dan konsistensi
* Mudah dikembangkan di masa depan

---

## 🏁 Penutup

Project ini dibuat dengan tujuan:

* Kode mudah dipahami
* Aturan bisnis jelas
* Perilaku sistem realistis
* Siap dikembangkan lebih lanjut

---