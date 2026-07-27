# Sistem booking lapangan badminton (Laravel)

Paket ini **bukan proyek Laravel utuh** — ini kumpulan file yang perlu ditempel
ke proyek Laravel + Breeze yang baru, supaya kamu (dan saya kalau nanti perlu
bantu debug) tahu persis apa yang perlu diinstal dan diletakkan di mana.

## 1. Buat proyek dasar

```bash
composer create-project laravel/laravel booking-badminton
cd booking-badminton

composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

Pilih stack **Blade** saat diminta `breeze:install` (bukan React/Vue) — semua
view di paket ini pakai komponen `<x-app-layout>` bawaan Breeze Blade, dan
Alpine.js untuk modal booking sudah otomatis ikut ter-install lewat stack ini.

## 2. Salin file dari paket ini

Salin isi folder-folder berikut ke lokasi yang sama persis di proyekmu
(timpa/tambahkan, jangan hapus file Breeze yang sudah ada):

```
config/badminton.php                         → config/
database/migrations/*.php                    → database/migrations/
database/seeders/CourtSeeder.php              → database/seeders/
app/Models/Court.php                          → app/Models/
app/Models/Booking.php                        → app/Models/
app/Http/Requests/StoreBookingRequest.php     → app/Http/Requests/
app/Http/Middleware/EnsureUserIsAdmin.php     → app/Http/Middleware/
app/Http/Controllers/BookingController.php    → app/Http/Controllers/
app/Http/Controllers/Admin/*.php              → app/Http/Controllers/Admin/
resources/views/booking/*.blade.php           → resources/views/booking/
resources/views/admin/**/*.blade.php          → resources/views/admin/
```

`routes/web.php` di paket ini **isinya contoh** — jangan ditimpa mentah-mentah.
Buka `routes/web.php` proyekmu, lalu:
1. Tambahkan bagian `use` dan `Route::middleware('auth')->group(...)` dari
   file contoh ke dalamnya.
2. Hapus route default `Route::get('/', ...)` bawaan Laravel supaya tidak
   bentrok dengan route booking yang baru.
3. Pastikan `require __DIR__.'/auth.php';` tetap ada di baris paling akhir.

## 3. Daftarkan middleware `admin`

**Laravel 11+** — di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['admin' => \App\Http\Middleware\EnsureUserIsAdmin::class]);
})
```

**Laravel 10 ke bawah** — di `app/Http/Kernel.php`, tambahkan ke `$middlewareAliases`:
```php
'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
```

## 4. Setup database

Atur kredensial database di `.env`, lalu:

```bash
php artisan migrate --seed
```

Kalau `CourtSeeder` belum otomatis jalan, tambahkan dulu ke
`database/seeders/DatabaseSeeder.php`:
```php
public function run(): void
{
    $this->call(CourtSeeder::class);
}
```

## 5. Jadikan satu akun sebagai admin

Register akun biasa lewat halaman `/register`, lalu jadikan admin lewat tinker:

```bash
php artisan tinker
>>> App\Models\User::where('email', 'emailkamu@contoh.com')->update(['role' => 'admin']);
```

## 6. Jalankan

```bash
npm run dev
php artisan serve
```

Buka `http://localhost:8000` — halaman utama langsung menampilkan grid
ketersediaan lapangan. Login sebagai admin untuk mengakses:
- `/admin/bookings` — kelola booking harian (tandai selesai / batalkan)
- `/admin/courts` — tambah/nonaktifkan/hapus lapangan

## Cara kerja intinya

- **Anti bentrok jadwal**: dicek dua kali — di level aplikasi (query cek
  konflik sebelum simpan) *dan* di level database lewat `unique(['court_id',
  'booking_date', 'start_time'])` pada tabel `bookings`. Jadi walau dua orang
  klik "Tersedia" di detik yang sama, cuma satu yang berhasil tersimpan.
- **Bayar di tempat**: booking langsung berstatus `confirmed` begitu dibuat
  (tidak ada langkah pembayaran online). Admin yang mengubah status jadi
  `completed` setelah pelanggan datang & bermain, atau `cancelled` kalau
  batal/tidak datang.
- **Jam operasional & durasi slot** diatur di `config/badminton.php` — ubah
  `open_time`, `close_time`, atau `slot_minutes` di sana kalau venue kamu
  bukan jam 08.00–23.00 atau ingin slot per 30 menit, dll.
