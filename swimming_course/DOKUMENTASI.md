# 📚 Dokumentasi Swimming Course

Aplikasi ini adalah platform manajemen kursus renang yang melayani dua aspek utama: halaman publik untuk pengunjung/calon peserta dan sistem internal (dashboard) untuk pengelolaan data peserta secara administratif. Seluruh kode saat ini berstatus bersih tanpa ada komentar yang tersemat pada skrip.

## 📋 Daftar Isi
1. [Struktur Proyek](#struktur-proyek)
2. [Fitur Utama](#fitur-utama)
3. [Alur Penggunaan (User & Admin)](#alur-penggunaan-user--admin)
4. [Tumpukan Teknologi (Tech Stack)](#tumpukan-teknologi-tech-stack)
5. [Skema Database](#skema-database)
# 📚 Dokumentasi Swimming Course Management System

## 📋 Daftar Isi
1. [Ringkasan Sistem](#ringkasan-sistem)
2. [Fitur Utama](#fitur-utama)
3. [Arsitektur & Teknologi](#arsitektur--teknologi)
4. [Struktur Database](#struktur-database)
5. [Struktur Folder](#struktur-folder)
6. [Cara Penggunaan](#cara-penggunaan)
7. [Keamanan](#keamanan)

---

## 📁 Struktur Proyek

Aplikasi ini menggunakan struktur PHP Native dengan pemisahan folder yang jelas untuk kemudahan pengelolaan.

- `assets/` dan `img/` - Berisi aset statis pendukung, berkas demo plugin, dan kumpulan gambar (seperti logo dan *background*).
- `config/` - Tempat menyimpan file konfigurasi, utamanya `database.php` untuk mengatur kredensial koneksi ke basis data MySQL.
- `css/` - Mengelola seluruh *styling* aplikasi secara modular (gaya untuk layar admin, landing, proses login, hingga register masing-masing berada di file terpisah).
- `dashboard/` - Halaman khusus admin yang diproteksi `session`. Digunakan untuk meninjau `data_siswa`, mengubah (`edit_registration.php`), atau menambah peserta (`tambah_peserta.php`). Bagian header/sidebar dirangkum dalam folder `partials/`.
- `database/` - Direktori inersia tempat melampirkan file skema `.sql` instalasi basis data awal.
- `js/` - Memuat skrip sisi-klien JavaScript untuk interaktivitas elemen dinamis (*mis.* animasi hero, pengalih input, dsb), utamanya via `landing-complete.js`.
- `proses/` - Berfungsi memuat rute dan logika *backend* esensial untuk autentikasi seperti `login.php`, `register.php`, dan `logout.php`.
- `sections/` - Komponen modular pembentuk *Landing Page* (meliputi elemen navigasi, fitur pilihan, rincian program harga, form pendaftaran umum, dan footer).
- File Skrip Dasar (*Root File*):
  - `index.php`: Halaman muka beranda bagi tamu publik yang dirajut dengan melakukan *require* pada komponen-komponen `sections/`.
  - `function.php`: Kumpulan fungsi fundamental utilitas sebagai perantara aplikasi dengan antarmuka basis data (pengambilan daftar peserta, penambahan registrasi, penanganan keamanan kueri, dsb).
Sistem ini adalah aplikasi manajemen administrasi **Swimming Course (Kursus Renang)** yang melayani dua entitas utama:
- **Pengunjung / User**: Dapat melihat informasi profil, jadwal, program kursus, dan melakukan registrasi secara online.
- **Admin**: Memiliki Dashboard khusus untuk memanajemen data mentah seperti Pendaftaran Siswa, Pembayaran, Jadwal Latihan, Master Data Kelas (Program), dan Laporan Keuangan.

---

## ✨ Fitur Utama

### 1. Modul Autentikasi (User & Admin)
✅ Registrasi pengguna baru dengan fitur enkripsi password yang aman.  
✅ Login dengan verifikasi password dan *Role-Based Access Control* (redirect otomatis antara Admin/User).  
✅ Proteksi *session* disetiap halaman.  

### 2. Landing Page & Form Registrasi (Public)
✅ Informasi Hero Section, Program yang otomatis tersinkron dengan database harga, Instrucs, dan Testimonial.  
✅ Form pendaftaran responsif yang memungkinkan pendaftar untuk memilih Program dan Jadwal Latihan yang berstatus "Aktif".

### 3. Admin Dashboard (Dinamis)
Aplikasi memisahkan antarmuka Admin dengan panel navigasi yang *sticky* dan dilengkapi komponen UI modern (SVG Icons).  
- 🧑‍🎓 **Data Siswa:** Manajemen CRUD peserta kursus beserta validasi dan animasi *count-up* statistik.
- 💳 **Pembayaran:** Manajemen status pembayaran pendaftar (Lunas, Pending, Batal).  
- 🗓️ **Jadwal Latihan:** Sistem CRUD dinamis untuk menambah, mengubah, dan menghapus jadwal kursus (*Waktu Mulai, Waktu Selesai, Status Aktif*).
- 🏊 **Data Kelas (Program/Harga):** Sistem CRUD dinamis untuk mengelola nama program kelas (Reguler, Privat, dll.) dan harganya secara *real-time*.
- 📊 **Laporan:** Dashboard analitik dan rekap keuangan masuk.
- 👥 **Manajemen User:** Mengelola akun pengguna serta *Role* (Admin/User).

---

## 🏗️ Arsitektur & Teknologi

### **Optimasi UI dan Kode:**
- **Pemisahan Logika & Tampilan (CSS/JS Eksternal)**: Menghindari penggunaan *inline CSS* maupun `<style>` HTML di dalam file. Semua instruktur styling dipindah ke direktori `assets/css/` dan logika dinamis interaktif ke `assets/js/`. Hal ini menciptakan struktur *Clean Code* yang ringan didownload oleh *browser*.
- **Ikon Berbasis SVG**: Membuang pemusatan penggunaan *emoji* bawaan sistem menjadi grafik berbasis SVG via fungsi PHP `icon('name', size)` di `function.php`, menghasilkan resolusi grafis yang konsisten pada semua browser dan sistem operasi.

---

## ✨ Fitur Utama

1. **Halaman Publik yang Interaktif berbasis *Scroll Snap***
   Melalui antarmuka satu-halaman (*single page*), pengunjung dapat melihat berbagai penawaran jenjang kelas program ("Programs"), jaminan kualitas instruktur ("Features"), serta formulir pendaftaran ("Registration") yang dirancang unik untuk merutekan kembali pendaftaran ke konfirmasi pesan WhatsApp secara rinci pasca pengajuan.

2. **Autentikasi Aman dan Kompatibel**
   Proses Pendaftaran (Register) memberlakukan pemeriksaan validitas sandi sekunder dan status email unik yang ada di basis data. Pembuatan *session login* sangat rapi dan kata sandi disimpan tersamar kuat (menggunakan *Bcrypt*). Seluruh ikon pendukung pada laman ini dibangun murni menggunakan elemen SVG standar.

3. **Dashboard Admin Tersentralisasi**
   Akun yang berlabel `admin` dialihkan ke area privat usai melakukan *login*. Admin tersebut leluasa merubah, menghapus, melihat metrik statistik pengguna, serta menginput data siswa/peserta baru secara lokal pada menu pengelola.

---

## 🔄 Alur Penggunaan (User & Admin)

**Sisi Pengunjung/Peserta (User):**
1. User dapat langsung mengisi form pendaftaran *bundle* di bagian tengah `index.php`.
2. Semasa tombol daftar ditekan dan tanpa kelalaian pengisian, pengguna dirangkum secara otomatis membuka *software* WhatsApp lokalnya dengan sebuah skrip ringkasan biaya tagihan pendaftaran kepada nomer admin kami.
3. User bebas membuat profil autentiknya sendiri melalui menu navigasi `Pendaftaran` atau sub-jalur integrasi `login` bila menghendaki eksplorasi portal pribadi nantinya.

**Sisi Administrator (Admin):**
1. Admin dapat *login* mengakses dasbor dengan membuka urutan `proses/login.php` dari akun yang di *set-up* memegang *role* `admin`.
2. Sukses masuk, Admin seketika mendarat di `dashboard/admin_dashboard.php`.
3. Di dalam ruang tersebut Admin mencatat, mengedit, serta menghapus peserta yang selesai diprospek pembayarannya melalui pesan kontak manual via WhatsApp yang diintegrasikan pada portal pengguna.

---

## 💻 Tumpukan Teknologi (Tech Stack)

Aplikasi dibangun tanpa kebergantungan terhadap *framework* eksternal yang masif dengan komitmen *native-first*.

- **Sisi Server**: PHP Native (Rekomendasi v.8.0.0 atau lebih terbaru)
- **Komponen Data**: MySQLi (Ekstensi Pangkalan Data Relasional)
- **Desain Tampilan**: HTML5, Vanilla CSS3 (dibentuk dari *layouting Flexbox/Grid* responsif), dan Vanilla JavaScript yang minimalis untuk pemicu fitur antar-muka interaktif.
- **Ikonografi & Aset**: Semua elemen grafis interaktif mengusung format kode SVG fleksibel (*Feather Icons*, dsb).

---

## 🗄️ Skema Database

Dasar pengerjaan dan pertukaran informasi pada sistem menumpu pada model basis data operasional multi-tabel, yang mana struktur penopang utamanya melibatkan entitas hak otorisasi akun.

**Tabel Dasar Terkait Autentikasi (`user`):**
- `id_user` *(Primary Key, Int, Auto Increment)*
- `name` *(Varchar, penama utuh pendaftar/pengelola)*
- `email` *(Varchar, identitas relasi utama untuk log identifikasi diri)*
- `password` *(Varchar 255-karakter, merangkum sidik komputasi kata sandi terselubung)*
- `role` *(Peran terstruktur `enum` membagi porsi tampilan antara hak otoriter berstatus `user` dan `admin`)*

*(Catatan: Aplikasi ini juga mengelaborasi data lain sesuai fungsinya seperti daftar detail partisipasi, penetapan nilai tagihan program per pilihan kelas (*tabel programs/jadwal*), yang dikoordinasi melingkar bersama fail `function.php`)*.
### 1. Tabel: `user`
Tabel untuk mengatur kredensial login.
- `id` (INT) PK
- `name` (VARCHAR)
- `email` (VARCHAR)
- `password` (VARCHAR) *(Hashed BCRYPT)*
- `role` (ENUM: 'admin', 'user')

### 2. Tabel: `jadwal`
Tabel *Master Data* jadwal latihan dinamis.
- `id_jadwal` (INT) PK
- `hari` (ENUM: 'Senin', 'Selasa', ... 'Minggu')
- `waktu_mulai` (TIME)
- `waktu_selesai` (TIME)
- `keterangan` (VARCHAR)
- `is_active` (TINYINT: 1 = Aktif, 0 = Nonaktif)

### 3. Tabel: `program`
Tabel *Master Data* program kelas dan harga.
- `id_program` (INT) PK
- `nama_program` (VARCHAR)
- `harga` (DECIMAL)
- `pertemuan` (INT)
- `deskripsi` (TEXT)
- `is_active` (TINYINT: 1 = Aktif, 0 = Nonaktif)

### 4. Tabel: `pendaftaran`
Tabel rekap data siswa.
- `id_peserta` (INT) PK
- `full_name`, `age`, `gender`, `whatsapp`, `status`
- Relasi Ke Tipe String *(Program & Jadwal)*

---

## 📁 Struktur Folder

``` text
swimming_course/
│
├── assets/
│   ├── css/                  # File-file styling eksternal (jadwal_latihan.css, dll.)
│   ├── js/                   # File-file logika script interaktif (dashboard.js, dll.)
│   └── img/                  # Folder gambar statis 
│
├── config/
│   └── database.php          # Koneksi MySQL
│
├── dashboard/
│   ├── partials/             # Navigasi & Sidebar partial untuk Admin
│   ├── admin_dashboard.php   # Main menu statistik admin
│   ├── data_kelas.php        # Halaman CRUD Program dinamis 
│   ├── jadwal_latihan.php    # Halaman CRUD Jadwal dinamis
│   ├── pembayaran.php
│   ├── laporan.php
│   └── manajemen_user.php
│
├── database/                 # Berisi migrasi raw `.sql`
│
├── proses/                   # Tempat memproses logika spesifik (login/register process)
│
├── sections/                 # Modul-modul UI Landing Page terpisah
│   ├── hero.php, programs.php, navbar.php, dll.
│
├── function.php              # Kumpulan logika database Helper & SVG Renderer
├── index.php                 # Halaman Landing Page Utama
├── login.php                 # Form Login
├── register.php              # Form Registrasi Baru
└── DOKUMENTASI.md            # File ini
```

---

## 🚀 Cara Penggunaan

### **Untuk Pengunjung / User:**
1. **Pendaftaran Akun:** Akses `/register.php` dan verifikasi data diri untuk membuat kredensial baru.
2. **Pendaftaran Kursus:** Pada Beranda (Landing Page), pilih menu daftar. Opsi Program dan Jadwal akan diload secara dinamis dari pengaturan kelas & jadwal di Admin Dashboard.
3. **Validasi Harga:** Harga akan merespon otomatis sesuai Program Kelas (Private / Reguler / Atlet) yang Anda pilih.

### **Untuk Administrator:**
1. **Akses Dashboard:** Silakan Login menggunakan `email: admin@swimming.com` (apabila Anda memiliki Role Admin).
2. **Manajemen Dinamis:**
   - Navigasi menuju halaman **Jadwal Latihan**. Anda dapat menambahkan shift waktu baru misalnya *(Jumat, 15:00 - 17:00)*. Anda dapat menyembunyikan jadwal tersebut dari user dengan tombol *Toggle Aktif/Nonaktif*.
   - Navigasi menuju halaman **Data Kelas**. Anda bisa merubah label harga *Real-Time*, menambah paket program baru, dan menghapus yang lama. Pendaftar umum akan otomatis mendapatkan update form sesuai dengan perubahan ini.
3. **Pantau Finansial:** Akses **Laporan** untuk melihat total uang masuk dan melihat metrik pembayaran menggunakan fitur cari dinamis dengan JavaScript.

---

## 🔒 Keamanan & Praktik Terbaik

1. **Password Hashing:** Sistem tidak pernah menyimpan *plain-text password*. Semua akun baru melewati skema algoritma autentikasi `PASSWORD_BCRYPT`.
2. **Standardisasi Akses (Session):** Memaksa peninjauan kredensial Admin dari awal (mencegah *URL-Forcing Redirect*).
   ```php
   if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
       header('Location: login.php');
       exit; 
   }
   ```
3. **Pemberantasan Inject (XSS & SQLi):** Seluruh parameter PHP mengandalkan `$stmt->prepare()` *(Prepared Statement)* dari *MySQLi* saat insersi data, serta mensterilkan penampakan nilai kembali dengan fungsi `htmlspecialchars()` pada input nama dan string lainnya.
4. **Modul Modular:** Penggunakan sintaksis pemanggilan parameter dan tag *UI Elements PHP* diformalkan via fungsi di `function.php`, mengurangi redundansi deklarativ penulisan tag HTML ganda.
