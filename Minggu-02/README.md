# Tugas Pemrograman Berbasis Kerangka Kerja (PBKK) W2

**Identitas Mahasiswa:**
* **Nama:** Achmad Najwa
* **NIM:** 5025231265
* **Departemen:** Teknik Informatika

---

## 1. Landasan Teori: Keunggulan Aplikasi Desktop

Meskipun aplikasi berbasis web dan seluler berkembang pesat, aplikasi desktop tetap menjadi pilihan utama untuk berbagai skenario kerja tertentu karena keunggulan berikut:
* **Efisiensi & Performa:** Eksekusi komputasi berat dan logika kompleks di sisi klien (*client-side*) berjalan jauh lebih cepat tanpa overhead latensi jaringan.
* **Integrasi Sistem & Hardware Lokal:** Akses langsung ke komponen perangkat keras (port serial, file system lokal, printer, periferal) tanpa restriksi izin browser atau hambatan firewall jaringan.
* **Manajemen Threading Efisien:** Pemanfaatan multithreading untuk tugas asinkron berjalan optimal langsung di atas manajemen thread sistem operasi lokal.
* **Kemudahan Pengembangan & Debugging:** Pengembangan antarmuka visual pada Windows Forms sangat intuitif melalui perancang formulir berbasis visual, didukung alat pelacakan galat (*debugging*) yang matang dan stabil.

---

## 2. HelloWorld!

### Langkah Pengerjaan

Jalankan kode berikut secara berurutan pada terminal:
```powershell
dotnet new console -n HelloWorld
cd HelloWorld
dotnet run
