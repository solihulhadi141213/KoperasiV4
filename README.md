# Deskripsi
Koperasi V4 adalah aplikasi koperasi (Aneka Usaha) berbasis web yang merupakan pembaharuan dari aplikasi koperasi versi sebelumnya (Versi 1,2 dan 3). 
Pada versi ini, pengembangan difokuskan pada optimalisasi database, fitur yang lebih dinamis serta kemudahan integrasi dengan berbagai platform. 

Harapan utama yang ingin dicapai pada pengembangan aplikasi Koperasi V4 ini diantaranya adalah : kemudahan pengelolaan data, tersajinya informasi yang lebih akurat, kemudahan navigasi dan peningkatan keamanan yang lebih baik.

Dengan adanya pembaharauan tersebut diharapkan aplikasi dapat digunakan lebih efektif, mudah digunakan dan efisiensi dalam pembiayaan operasionalnya. Dengan menggunakan versi PHP terbaru (PHP 8x) diharapkan aplikasi dapat berjalan dengan baik pada lingkungan server moderen.


# Spesifikasi
 - PHP 8.x
 - MySql 9.1.0
 - Apache 2.4.62.1

# Fitur Aplikasii

    ## General
    - Halaman Login + Include Captcha
    - Profil Pengguna
    - Bantuan

    ## Aksesibilitas
    - Fitur Aplikasi
    - Entitas Akses
    - Akses Pengguna

    ##  Anggota
    - Manajemen Anggota
    - Rekapitulasi Anggota Masuk - Keluar
    - Grouping Anggota

    ## Simpanan
    - Referensi Jenis Simpanan Anggota
    - Data Simpanan Anggota
    - Penarikan Dana Simpanan

    ## Barang (Inventory)
    - Master Data barang
    - Batch & Expired Date

# Instalasi
Untuk instalasi aplikasi ini, ada 4 hal yang perlu anda ketahui :
 - Instalasi Webserver
 - Memasang Directory aplikasi pada webserver
 - Import Database
 - Setting Aplikasi

Apabila anda melewatkan ke 4 hal tersebut maka kemungkinan aplikasi tidak akan berjalan dengan baik.

## Instalasi Webserver
Secara sederhana, web server adalah perangkat lunak (software) yang berfungsi menerima permintaan (request) berupa halaman web melalui protokol HTTP/HTTPS dari pengguna (via browser seperti Chrome atau Edge), lalu mengirimkan kembali respon tersebut dalam bentuk halaman situs web (biasanya file HTML, CSS, JavaScript, atau gambar).

Jika web server tidak berjalan, komputer Anda tidak akan bisa membaca kode PHP atau database SQL sebagai sebuah website utuh.

Banyak sekali aplikasi webserver tersebut, namun saya contohkan instalasi populer menggunakan xampp atau wampServer (Pilih salah satu saja)

### 1. Cara Instalasi XAMPP (Windows/Linux/macOS)
   XAMPP adalah paket cross-platform yang sangat populer karena bisa digunakan di berbagai sistem operasi.
  - Unduh Installer: Buka situs resmi Apache Friends dan unduh installer XAMPP yang sesuai dengan sistem operasi Anda.
  - Jalankan Installer: Klik ganda pada file yang sudah diunduh. Jika muncul peringatan User Account Control (UAC) atau Important Warning, klik OK/Yes saja untuk melanjutkan.
  - Pilih Komponen: Pada menu Select Components, centang komponen yang dibutuhkan. Minimal centang Apache, MySQL, PHP, dan phpMyAdmin. Klik Next.
  - Pilih Folder Instalasi: Tentukan lokasi folder (defaultnya di C:\xampp untuk Windows). Klik Next.
  - Proses Instalasi: Klik Next terus hingga proses instalasi berjalan. Tunggu beberapa menit hingga selesai.
  - Selesai & Jalankan: Klik Finish. Control Panel XAMPP akan terbuka. Klik tombol Start pada Apache dan MySQL untuk mulai menggunakannya.

### 2. Cara Instalasi WampServer (Khusus Windows)

WampServer dirancang khusus untuk sistem operasi Windows dan sangat fleksibel jika Anda sering berganti versi PHP.

    ⚠️ Penting sebelum instal: WampServer membutuhkan komponen Microsoft Visual C++ Redistributable (VC++) versi terbaru di Windows Anda. Pastikan komponen ini sudah terinstal agar tidak terjadi error missing DLL saat instalasi.

  - Unduh Installer: Buka situs resmi WampServer dan unduh versi yang sesuai dengan arsitektur Windows Anda (64-bit atau 32-bit).
  - Jalankan Installer: Klik kanan pada file installer dan pilih Run as administrator. Pilih bahasa (Inggris/Prancis) lalu klik OK.
  - Persetujuan Lisensi: Pilih I accept the agreement, lalu klik Next.
  - Informasi VC++: Installer akan menampilkan daftar komponen Visual C++ yang dibutuhkan. Jika Anda yakin sudah menginstalnya, klik Next.
  - Pilih Lokasi & Komponen: Tentukan folder instalasi (default di C:\wamp64 atau C:\wamp). Anda juga bisa memilih versi PHP atau MySQL spesifik yang ingin ikut diinstal. Klik Next.
  - Proses Instalasi: Klik Install dan tunggu hingga proses ekstraksi file selesai.
  - Pilih Browser & Text Editor Default: Di tengah proses, WampServer akan bertanya apakah Anda ingin mengubah browser default (defaultnya Internet Explorer/Edge) dan text editor (defaultnya Notepad). Pilih Yes jika ingin mengubahnya ke Chrome/VS Code, atau No jika ingin memakai bawaan.
  - Selesai: Klik Next lalu Finish. Untuk menjalankannya, buka shortcut WampServer di desktop. Indikator warna hijau pada system tray (pojok kanan bawah) menandakan semua service telah berjalan dengan lancar.

## Memasang Directory aplikasi pada webserver
## Import Database
## Setting Aplikasi

# Alur Kerja
## Modal Koperasi
Modal koperasi berasal dari simpanan anggota dan sumber lain. Berbeda dengan model bisnis lain, simpanan anggota yang dikumpulkan dan dikelola untuk membangun satu kesatuan unit usaha.

# Referensi
