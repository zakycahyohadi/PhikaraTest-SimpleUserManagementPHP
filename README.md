# Aplikasi Manajemen User Sederhana (PHP & MySQL)

Aplikasi ini merupakan sistem manajemen user sederhana yang dibuat menggunakan PHP dan MySQL. Fitur-fitur utama mencakup login dengan security image (captcha), daftar user, serta operasi CRUD (Create, Read, Update, Delete) untuk user.

## 🔒 Fitur Utama

- Form Login dengan verifikasi captcha (security image)
- Validasi login (username & password match)
- Form Daftar User
- Form Tambah, Ubah, dan Hapus User
- Password dienkripsi menggunakan `password_hash()`
- Validasi panjang password (5–8 karakter)

## 🛠️ Struktur Tabel `tbl_user`

- `id` – INT, Primary Key, Auto Increment
- `username` – VARCHAR(128), Unik
- `password` – VARCHAR(54), Terenkripsi
- `CreateTime` – DATETIME

## 📸 Screenshot Aplikasi

**Form Login**

![Form Login](SS/Screenshot%202025-06-03%20at%2014.26.37.png)

**Form Tambah User**

![Form Tambah User](SS/Screenshot%202025-06-03%20at%2014.26.44.png)

**Form Daftar User**

![Form Daftar User](SS/Screenshot%202025-06-03%20at%2014.26.55.png)

**Form Edit User**

![Form Edit User](SS/Screenshot%202025-06-03%20at%2014.27.20.png)

---

© 2025 – Dibuat untuk keperluan ujian/praktik pembuatan aplikasi CRUD sederhana.
