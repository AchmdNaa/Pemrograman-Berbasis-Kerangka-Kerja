# 📚 Tugas PBKK - Minggu ke-2: Latihan 1 (Aplikasi Desktop)

> **Mata Kuliah:** Pemrograman Berbasis Kerangka Kerja
> **Platform:** .NET 10 & C#

---

## 1. Landasan Teori: Keunggulan Aplikasi Desktop

Meskipun aplikasi berbasis web dan seluler berkembang pesat, aplikasi desktop tetap menjadi pilihan utama untuk berbagai skenario kerja tertentu karena keunggulan berikut:

* **Efisiensi & Performa:** Eksekusi komputasi berat dan logika kompleks di sisi klien (*client-side*) berjalan jauh lebih cepat tanpa overhead latensi jaringan.
* **Integrasi Sistem & Hardware Lokal:** Akses langsung ke komponen perangkat keras (port serial, file system lokal, printer, periferal) tanpa restriksi izin browser atau hambatan firewall jaringan.
* **Manajemen Threading Efisien:** Pemanfaatan multithreading untuk tugas asinkron berjalan optimal langsung di atas manajemen thread sistem operasi lokal.
* **Kemudahan Pengembangan & Debugging:** Pengembangan antarmuka visual pada Windows Forms sangat intuitif melalui perancang formulir berbasis visual, didukung alat pelacakan galat (*debugging*) yang matang dan stabil.

---

## 2. Program 1: Sistem Data Mahasiswa (Console Application)

Program ini menerapkan konsep Object-Oriented Programming (OOP) untuk mengelola data mahasiswa (*NIM, Nama, Program Studi, IPK*) menggunakan koleksi dinamis `List<Mahasiswa>`.

### Cara Menjalankan

```bash
cd DataMahasiswa
dotnet run
```
