# 📚 Dokumentasi Sistem Autentikasi - Swimming Course

## 📋 Daftar Isi
1. [Ringkasan Sistem](#ringkasan-sistem)
2. [Struktur Database](#struktur-database)
3. [Alur Pengerjaan](#alur-pengerjaan)
4. [Penjelasan Fitur](#penjelasan-fitur)
5. [Struktur File](#struktur-file)
6. [Cara Penggunaan](#cara-penggunaan)
7. [Keamanan](#keamanan)

---

## 🎯 Ringkasan Sistem

Sistem autentikasi ini adalah bagian dari aplikasi manajemen kursus renang yang memiliki dua jenis pengguna:
- **Admin**: Dapat mengelola data pendaftaran peserta
- **User**: Dapat mendaftar dan melihat informasi kursus

### Fitur Utama:
✅ Registrasi pengguna baru dengan password terenkripsi  
✅ Login dengan verifikasi password yang aman  
✅ Redirect otomatis berdasarkan role (admin/user)  
✅ Session management untuk keamanan  
✅ Logout dengan pembersihan session  
✅ CSS terpisah untuk maintainability yang lebih baik  

---

## 🗄️ Struktur Database

### Tabel: `user`
```sql
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Kolom:**
- `id`: ID unik untuk setiap user
- `name`: Nama lengkap user
- `email`: Email (digunakan untuk login)
- `password`: Password yang sudah di-hash
- `role`: Peran user (admin atau user biasa)
- `created_at`: Waktu pembuatan akun

**Data Default:**
```sql
-- Admin default
Email: admin@swimming.com
Password: admin123
Role: admin
```

---

## 🔄 Alur Pengerjaan

### 1️⃣ **Fase Analisis & Perencanaan**
**Tujuan:** Memahami kebutuhan sistem autentikasi

**Yang Dilakukan:**
- Menganalisis sistem yang sudah ada (`login.php`, `index.php`)
- Mengidentifikasi masalah keamanan (password plain text di SQL)
- Merencanakan fitur yang dibutuhkan (registrasi, login aman, role-based access)

**Hasil:**
- Dokumen `implementation_plan.md` yang berisi rencana perubahan

---

### 2️⃣ **Fase Implementasi - Registrasi User**
**Tujuan:** Membuat halaman registrasi untuk user baru

**Yang Dilakukan:**

**A. Membuat File `register.php`**
```php
// Fitur utama:
1. Form registrasi (Nama, Email, Password, Konfirmasi Password)
2. Validasi input (field kosok, password match, panjang password)
3. Cek email duplikat di database
4. Hash password menggunakan password_hash()
5. Insert user baru dengan role 'user'
6. Redirect ke login.php dengan pesan sukses
```

**B. Validasi yang Diterapkan:**
- ✅ Semua field harus diisi
- ✅ Password minimal 6 karakter
- ✅ Password dan konfirmasi harus sama
- ✅ Email tidak boleh duplikat

**C. Keamanan:**
- Password di-hash dengan `PASSWORD_DEFAULT` (bcrypt)
- Input di-sanitasi dengan `trim()` dan `htmlspecialchars()`
- Prepared statement untuk mencegah SQL injection

**Hasil:**
- File `register.php` yang aman dan user-friendly

---

### 3️⃣ **Fase Implementasi - Perbaikan Login**
**Tujuan:** Memperbaiki sistem login agar lebih aman

**Masalah Awal:**
```php
// ❌ TIDAK AMAN - Password dicek langsung di SQL
$sql = "SELECT * FROM user WHERE email='$email' AND password='$password'";
```

**Solusi yang Diterapkan:**

**A. Perubahan Logika Login**
```php
// ✅ AMAN - Ambil user dulu, lalu verifikasi password
1. Query user berdasarkan email saja
2. Gunakan password_verify() untuk cek password
3. Tambahkan fallback untuk password plain text (untuk admin lama)
4. Set session variables jika login berhasil
5. Redirect berdasarkan role
```

**B. Kode Implementasi:**
```php
// 1. Ambil data user
$stmt = $conn->prepare("SELECT id, name, email, password, role FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// 2. Verifikasi password
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Cek dengan password_verify ATAU plain text (untuk backward compatibility)
    if (password_verify($password, $user['password']) || $password === $user['password']) {
        // Login berhasil
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect berdasarkan role
        if ($user['role'] == 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: index.php');
        }
    }
}
```

**C. Fitur Tambahan:**
- Pesan sukses setelah registrasi
- Pesan sukses setelah logout
- Error handling yang jelas

**Hasil:**
- Login yang aman dengan password hashing
- Backward compatibility untuk admin lama
- Role-based redirection

---

### 4️⃣ **Fase Implementasi - Update Dashboard User**
**Tujuan:** Menampilkan informasi user yang login

**Yang Dilakukan:**

**A. Update Navigation Bar (`index.php`)**
```php
// Jika user login:
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    // Tampilkan: "Hai, [Nama User]" dan tombol Logout
} else {
    // Tampilkan: Link Login dan Daftar Sekarang
}
```

**B. Pre-fill Form Registrasi**
```php
// Jika user sudah login, isi otomatis nama di form
value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>"
```

**Hasil:**
- User experience yang lebih baik
- Navigasi dinamis berdasarkan status login

---

### 5️⃣ **Fase Debugging - Fix Redirect Loop**
**Tujuan:** Memperbaiki error "Too many redirects" di admin dashboard

**Masalah:**
```php
// ❌ admin_dashboard.php mengecek session yang berbeda
if (!isset($_SESSION['admin_logged_in'])) { ... }

// ✅ Tapi login.php set session ini
$_SESSION['user_logged_in'] = true;
```

**Solusi:**
```php
// Standardisasi session variables
// Semua file menggunakan:
$_SESSION['user_logged_in']  // untuk status login
$_SESSION['role']            // untuk cek role
$_SESSION['user_name']       // untuk nama
$_SESSION['user_email']      // untuk email
```

**Perubahan di `admin_dashboard.php`:**
```php
// Sebelum:
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
}

// Sesudah:
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
}
```

**Hasil:**
- Admin dapat login tanpa redirect loop
- Session management yang konsisten

---

### 6️⃣ **Fase Refactoring - Ekstraksi CSS**
**Tujuan:** Memisahkan CSS ke file eksternal untuk maintainability

**Yang Dilakukan:**

**A. File yang Direfactor:**
1. `login.php` → `css/login.css`
2. `register.php` → `css/register.css`
3. `edit_registration.php` → `css/edit_registration.css`
4. `admin_dashboard.php` → `css/admin_dashboard.css`

**B. Proses Refactoring:**
```
1. Ekstrak semua CSS dari tag <style> di file PHP
2. Buat file CSS baru di folder css/
3. Ganti tag <style> dengan <link rel="stylesheet">
4. Test untuk memastikan styling tetap sama
```

**C. Keuntungan:**
- ✅ Kode lebih bersih dan terorganisir
- ✅ CSS dapat di-cache oleh browser
- ✅ Mudah untuk maintenance
- ✅ Dapat digunakan ulang di file lain

**Hasil:**
- 4 file CSS terpisah yang rapi
- File PHP lebih ringkas

---

### 7️⃣ **Fase Version Control**
**Tujuan:** Menyimpan perubahan ke GitHub

**Yang Dilakukan:**
```bash
# 1. Cek status
git status

# 2. Commit perubahan
git commit -m "Refactor: Extract CSS to external files for better maintainability"

# 3. Push ke GitHub
git push origin main
```

**Hasil:**
- Semua perubahan tersimpan di repository
- History yang jelas untuk tracking

---

## 🎨 Penjelasan Fitur

### 1. **Registrasi User (`register.php`)**

**Cara Kerja:**
1. User mengisi form (Nama, Email, Password)
2. Sistem validasi input
3. Cek apakah email sudah terdaftar
4. Hash password dengan bcrypt
5. Simpan ke database dengan role 'user'
6. Redirect ke halaman login

**Keamanan:**
- Password di-hash sebelum disimpan
- Validasi email duplikat
- Prepared statement untuk SQL

---

### 2. **Login (`login.php`)**

**Cara Kerja:**
1. User input email dan password
2. Sistem cari user berdasarkan email
3. Verifikasi password dengan `password_verify()`
4. Jika benar, buat session dan redirect
5. Admin → `admin_dashboard.php`
6. User → `index.php`

**Fitur Khusus:**
- Fallback untuk password plain text (admin lama)
- Pesan sukses dari registrasi/logout
- Error handling yang jelas

---

### 3. **Dashboard Admin (`admin_dashboard.php`)**

**Cara Kerja:**
1. Cek session dan role admin
2. Tampilkan statistik pendaftaran
3. Tampilkan tabel data peserta
4. Fitur edit dan hapus data

**Proteksi:**
- Hanya admin yang bisa akses
- Redirect ke login jika bukan admin

---

### 4. **Edit Registrasi (`edit_registration.php`)**

**Cara Kerja:**
1. Ambil ID dari URL
2. Load data pendaftaran
3. Tampilkan form dengan data existing
4. Update data saat submit
5. Redirect kembali ke dashboard

---

## 📁 Struktur File

```
swimming_course/
│
├── config/
│   └── database.php          # Konfigurasi database
│
├── css/
│   ├── admin_dashboard.css   # Style untuk admin dashboard
│   ├── edit_registration.css # Style untuk edit form
│   ├── login.css             # Style untuk login
│   ├── register.css          # Style untuk registrasi
│   └── landing-complete.css  # Style untuk landing page
│
├── database/
│   └── swimming_course.sql   # Schema database
│
├── admin_dashboard.php       # Dashboard admin
├── edit_registration.php     # Form edit pendaftaran
├── function.php              # Helper functions
├── index.php                 # Landing page & user dashboard
├── login.php                 # Halaman login
├── logout.php                # Proses logout
├── register.php              # Halaman registrasi
└── DOKUMENTASI.md            # File ini
```

---

## 🚀 Cara Penggunaan

### **Untuk User Baru:**

1. **Registrasi**
   - Buka: `http://localhost/swimming_course/register.php`
   - Isi form registrasi
   - Klik "Daftar Sekarang"
   - Anda akan diarahkan ke halaman login

2. **Login**
   - Buka: `http://localhost/swimming_course/login.php`
   - Masukkan email dan password
   - Klik "Login"
   - Anda akan diarahkan ke halaman utama

3. **Logout**
   - Klik tombol "Logout" di navigation bar
   - Session akan dihapus
   - Anda akan diarahkan kembali ke login

---

### **Untuk Admin:**

1. **Login sebagai Admin**
   - Email: `admin@swimming.com`
   - Password: `admin123`
   - Klik "Login"
   - Anda akan diarahkan ke Admin Dashboard

2. **Kelola Data Pendaftaran**
   - Lihat statistik di dashboard
   - Edit data dengan klik tombol "Edit"
   - Hapus data dengan klik tombol "Hapus"

3. **Tambah Pendaftaran Baru**
   - Klik "+ Tambah Pendaftaran"
   - Isi form di halaman utama
   - Data akan muncul di dashboard

---

## 🔒 Keamanan

### **Fitur Keamanan yang Diterapkan:**

1. **Password Hashing**
   ```php
   // Saat registrasi
   $hashed_password = password_hash($password, PASSWORD_DEFAULT);
   
   // Saat login
   password_verify($password, $user['password'])
   ```

2. **Prepared Statements**
   ```php
   // Mencegah SQL Injection
   $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
   $stmt->bind_param("s", $email);
   ```

3. **Input Sanitization**
   ```php
   $name = trim($_POST['name']);
   echo htmlspecialchars($user['name']);
   ```

4. **Session Management**
   ```php
   session_start();
   $_SESSION['user_logged_in'] = true;
   ```

5. **Role-Based Access Control**
   ```php
   if ($_SESSION['role'] !== 'admin') {
       header('Location: login.php');
   }
   ```

---

## 📝 Catatan Penting

### **Backward Compatibility:**
Sistem mendukung admin lama dengan password plain text:
```php
if (password_verify($password, $user['password']) || $password === $user['password']) {
    // Login berhasil
}
```

⚠️ **Rekomendasi:** Sebaiknya admin lama mengganti password mereka agar ter-hash.

### **Session Variables:**
Semua file menggunakan session variables yang sama:
- `$_SESSION['user_logged_in']` - Status login
- `$_SESSION['user_id']` - ID user
- `_SESSION['user_name']` - Nama user
- `$_SESSION['user_email']` - Email user
- `$_SESSION['role']` - Role (admin/user)

---

## 🛠️ Troubleshooting

### **Masalah: "Too many redirects"**
**Solusi:** Pastikan semua file menggunakan session variables yang sama

### **Masalah: "Password salah" padahal benar**
**Solusi:** Cek apakah password di database sudah di-hash atau masih plain text

### **Masalah: CSS tidak muncul**
**Solusi:** Pastikan path CSS benar: `href="css/nama-file.css"`

---

## 📞 Support

Jika ada pertanyaan atau masalah, silakan hubungi developer atau buat issue di repository GitHub.

---

**Dibuat dengan ❤️ untuk Swimming Course Management System**
