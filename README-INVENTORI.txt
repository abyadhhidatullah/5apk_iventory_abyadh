IVENTORI — PROFESSIONAL MODULAR EDITION

STACK: PHP Native, MySQL, mysqli prepared statements.
DATABASE: iventory
TABEL: barang, barangmasuk, kategori, merek, penyuplai, user.

STRUKTUR:
- index.php: router dan kerangka aplikasi
- config/koneksi.php: koneksi MySQL
- auth/login.php, auth/logout.php: autentikasi
- includes/fungsi.php: helper dan opsi relasi
- includes/definisi.php: konfigurasi field per tabel
- pages/dashboard.php: ringkasan stok dan aktivitas
- pages/tampilan/: layar data terpisah
- pages/component/: komponen tampilan dan formulir CRUD
- proses/: handler CRUD
- assets/css/style.css, assets/js/app.js: tema dan interaksi
- Database/: struktur referensi dan SQL migrasi password

INSTALASI:
1. Ekstrak ke folder web server lokal.
2. Nyalakan Apache dan MySQL.
3. Gunakan database iventory yang sudah berisi enam tabel sesuai Database/descmysql.txt.
4. Jalankan Database/perbaikan_password.sql sekali di phpMyAdmin agar password hash dapat disimpan.
5. Sesuaikan config/koneksi.php dengan akun MySQL lokal.
6. Buka http://localhost/IVENTORI-Pink-Modular/.

LOGIN:
- Gunakan akun yang sudah ada pada tabel user.
- Password lama teks biasa didukung untuk kompatibilitas dan di-hash otomatis setelah migrasi kolom.
- Password yang dibuat dari form pengguna baru disimpan menggunakan password_hash().
- Struktur tabel user pada proyek awal tidak memiliki kolom role; aplikasi menggunakan satu dashboard untuk akun yang terdaftar.

PERILAKU STOK:
- Pencatatan barang masuk menambah stok.
- Edit barang masuk mengurangi jumlah transaksi lama lalu menambahkan jumlah terbaru.
- Penghapusan transaksi barang masuk mengurangi stok transaksi tersebut.
- Penghapusan barang dengan riwayat masuk dicegah.

KEAMANAN DAN UJI:
- Operasi CRUD menggunakan prepared statements.
- Output ditampilkan melalui escaping HTML.
- Coba dahulu dengan salinan database sebelum digunakan untuk data penting.
- PHP files should be checked using `php -l`; runtime still depends on the local PHP/MySQL configuration.
