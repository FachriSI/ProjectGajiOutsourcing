# 🚀 Cara Menjalankan Project Gaji Outsourcing

Panduan lengkap untuk menjalankan aplikasi **Project Gaji Outsourcing** menggunakan Laravel dan Laragon.

---

## 📋 Prasyarat

Pastikan sudah terinstall:
- ✅ **Laragon** (dengan PHP 8.3+ dan MySQL)
- ✅ **Composer** (biasanya sudah included di Laragon)
- ✅ Browser (Chrome/Firefox/Edge)

---

## 🔧 Tahapan Setup Project

### **1. Pastikan Laragon Berjalan**

1. Buka aplikasi **Laragon**
2. Klik **Start All** untuk menjalankan Apache & MySQL
3. Pastikan kedua service berjalan (lampu hijau)

### **2. Buka Terminal di Folder Project**

```bash
cd "d:\kp sp\PROJECT 2 COMEL\ProjectGajiOutsourcing"
```

### **3. Install Dependencies (Jika Belum)**

Hanya perlu dilakukan sekali atau setelah pull update yang mengubah `composer.json`:

```bash
composer install
```

### **4. Setup File Environment**

File `.env` sudah ada dan terkonfigurasi. Pastikan isinya seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dataoutsourcingnew
DB_USERNAME=root
DB_PASSWORD=
```

### **5. Generate Application Key** 

Jika `APP_KEY` kosong di file `.env`, jalankan:

```bash
php artisan key:generate
```

✅ **Status:** Sudah berhasil dijalankan!

### **6. Setup Database**

#### A. Buat Database di phpMyAdmin

1. Buka browser, akses: http://localhost/phpmyadmin
2. Klik tab **Database**
3. Buat database baru dengan nama: `dataoutsourcingnew`
4. Collation: `utf8mb4_general_ci`
5. Klik **Create**

#### B. Import Database (Jika Ada File SQL)

Jika file `dataoutsourcingnew (2).sql` ada di folder project:

1. Di phpMyAdmin, pilih database `dataoutsourcingnew`
2. Klik tab **Import**
3. Choose file: pilih `dataoutsourcingnew (2).sql`
4. Klik **Go**

**ATAU** bisa gunakan migrations Laravel:

```bash
php artisan migrate
```

✅ **Status:** Migrasi sudah berhasil dijalankan!

### **7. Buat User Admin** (Opsional)

Jika ada file `create_admin.php` di root project:

```bash
php create_admin.php
```

---

## ▶️ Menjalankan Aplikasi

### **Cara 1: Menggunakan Laravel Development Server**

Jalankan perintah ini di terminal:

```bash
php artisan serve
```

Atau dengan path lengkap PHP dari Laragon:

```bash
c:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe artisan serve
```

✅ **Aplikasi berjalan di:** http://127.0.0.1:8000

✅ **Status:** Server sudah berjalan!

### **Cara 2: Menggunakan Laragon Virtual Host** (Rekomendasi)

1. Di Laragon, klik kanan > **Apache** > **Add virtual host**
2. Nama: `ProjectGajiOutsourcing` → URL akan jadi: http://projectgajioutsourcing.test
3. Path: `d:\kp sp\PROJECT 2 COMEL\ProjectGajiOutsourcing\public`
4. Restart Laragon

✅ **Aplikasi berjalan di:** http://projectgajioutsourcing.test

---

## 🌐 Mengakses Aplikasi

Buka browser dan akses:

- **Development Server:** http://127.0.0.1:8000
- **Virtual Host:** http://projectgajioutsourcing.test (jika sudah setup)

---

## 🔄 Setelah Git Pull (Update dari Repository)

Setiap kali melakukan `git pull origin main`, jalankan:

```bash
# 1. Update dependencies jika ada perubahan
composer install

# 2. Jalankan migrasi database baru
php artisan migrate

# 3. Clear cache (opsional tapi direkomendasikan)
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 4. Restart server
php artisan serve
```

---

## 🛠️ Troubleshooting

### Problem: "php is not recognized"

**Solusi:** Gunakan path lengkap PHP dari Laragon:

```bash
c:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe artisan serve
```

### Problem: Database connection error

**Solusi:**
1. Pastikan MySQL di Laragon sudah running
2. Pastikan database `dataoutsourcingnew` sudah dibuat
3. Cek kredensial di file `.env`

### Problem: Blank page atau error 500

**Solusi:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Problem: Migration error

**Solusi:**

```bash
php artisan migrate:fresh
```

**⚠️ WARNING:** Perintah di atas akan menghapus semua data!

---

## 📦 Fitur-Fitur Terbaru (Setelah Git Pull)

Berdasarkan update terakhir, project ini sudah memiliki:

✅ **Autentikasi**
- Login page baru (`resources/views/auth/login.blade.php`)
- AuthController untuk handling login/logout

✅ **Kalkulator Kontrak**
- Halaman kalkulator untuk menghitung nilai kontrak
- Service `ContractCalculatorService` untuk logika perhitungan
- History kontrak tracking

✅ **Import Data**
- Import karyawan baru via Excel
- Import data perusahaan via Excel

✅ **Database Baru**
- Tabel `nilai_kontrak` untuk menyimpan nilai kontrak
- Tabel `kontrak_history` untuk tracking perubahan kontrak
- Kolom baru di tabel `karyawan` dan `perusahaan`

---

## 📞 Bantuan

Jika ada masalah atau pertanyaan:
1. Check error di terminal/console
2. Check Laravel log: `storage/logs/laravel.log`
3. Pastikan semua service Laragon berjalan

---

**Happy Coding! 🎉**
