# Tugas Pemrograman Berbasis Kerangka Kerja (PBKK) — Minggu 1

**Nama:** Achmad Najwa
**NIM:** 5025231265
**Kelas:** PBKK D
**Departemen:** Teknik Informatika

---

## Daftar Isi

1. [Apa Itu Framework (Kerangka Kerja)?](#1-apa-itu-framework-kerangka-kerja)
2. [Mengenal .NET Framework dan .NET Modern](#2-mengenal-net-framework-dan-net-modern)
3. [Arsitektur .NET](#3-arsitektur-net)
4. [Persiapan Lingkungan Pengembangan](#4-persiapan-lingkungan-pengembangan)
5. [Latihan: Hello World dengan .NET CLI](#5-latihan-hello-world-dengan-net-cli)
6. [Kesimpulan](#6-kesimpulan)

---

## 1. Apa Itu Framework (Kerangka Kerja)?

Framework adalah kerangka kerja yang menyediakan struktur, pustaka (*library*), dan aturan baku sehingga developer tidak perlu membangun aplikasi dari nol. Developer cukup "mengisi" bagian yang spesifik untuk kebutuhan aplikasinya, sementara hal umum seperti akses database, tampilan, routing, dan keamanan sudah disediakan.

Perbedaan utama **library** dan **framework** ada pada *inversion of control*:

| Aspek | Library | Framework |
|---|---|---|
| Siapa yang memanggil | Kode kita memanggil library | Framework memanggil kode kita |
| Struktur proyek | Bebas | Mengikuti konvensi framework |
| Contoh | Newtonsoft.Json, jQuery | .NET (WPF, ASP.NET), CodeIgniter, Laravel |

Manfaat menggunakan framework:

- **Produktivitas** — komponen umum sudah tersedia, waktu pengembangan lebih singkat.
- **Konsistensi** — struktur kode seragam sehingga mudah dipahami tim.
- **Keamanan** — fitur seperti validasi input, proteksi CSRF, dan hashing password sudah disediakan.
- **Pemeliharaan** — pemisahan tanggung jawab (misalnya pola MVC/MVVM) memudahkan perbaikan dan pengembangan.

## 2. Mengenal .NET Framework dan .NET Modern

**.NET** adalah platform pengembangan dari Microsoft untuk membangun aplikasi desktop, web, mobile, cloud, hingga game. Perjalanannya:

| Generasi | Tahun | Keterangan |
|---|---|---|
| .NET Framework 1.0 – 4.8.1 | 2002 – sekarang | Hanya berjalan di Windows. Masih dipakai untuk aplikasi lama (WinForms, WPF, ASP.NET Web Forms). |
| .NET Core 1.0 – 3.1 | 2016 – 2019 | Ditulis ulang, *open source* dan *cross-platform* (Windows, Linux, macOS). |
| .NET 5, 6, 7, 8, 9, 10 | 2020 – sekarang | Penyatuan .NET Framework dan .NET Core menjadi satu platform. Versi genap (8, 10) adalah LTS. |

Jenis aplikasi yang dapat dibuat dengan .NET:

- **Desktop** — Windows Forms, WPF, WinUI, .NET MAUI.
- **Web** — ASP.NET Core (MVC, Razor Pages, Blazor, Web API).
- **Mobile** — .NET MAUI (Android, iOS).
- **Cloud, IoT, Game** — Azure Functions, Unity (C#).

## 3. Arsitektur .NET

```
 Kode C# / F# / VB.NET
          │  dikompilasi oleh compiler (Roslyn)
          ▼
 Intermediate Language (IL) + Metadata  ──►  file .dll / .exe (assembly)
          │  dijalankan oleh
          ▼
 Common Language Runtime (CLR)
   ├─ JIT Compiler     : IL → kode mesin saat runtime
   ├─ Garbage Collector: manajemen memori otomatis
   ├─ Type Safety & Exception Handling
   └─ Security
          │  memakai
          ▼
 Base Class Library (BCL): System, System.IO, System.Collections, System.Linq, ...
```

- **CLR (Common Language Runtime)** — mesin eksekusi .NET. Mengubah IL menjadi kode mesin dengan JIT, mengelola memori lewat *garbage collector*, serta menangani *exception*.
- **BCL / FCL (Base/Framework Class Library)** — kumpulan kelas siap pakai: koleksi data, file, jaringan, database (ADO.NET), dan antarmuka (WinForms/WPF).
- **CTS & CLS** — standar tipe data dan aturan bahasa, sehingga kode C#, F#, dan VB.NET bisa saling dipakai.

## 4. Persiapan Lingkungan Pengembangan

| Perangkat | Kegunaan |
|---|---|
| .NET SDK 8 / 9 | Compiler, runtime, dan CLI `dotnet` |
| Visual Studio Code + C# Dev Kit | Editor kode (atau Visual Studio 2022 dengan workload *.NET desktop development*) |
| Windows 10/11 | Wajib untuk menjalankan WinForms dan WPF |

Cek instalasi SDK:

```powershell
dotnet --version
dotnet --list-sdks
```

Beberapa perintah CLI yang sering dipakai:

| Perintah | Fungsi |
|---|---|
| `dotnet new list` | Melihat template proyek yang tersedia |
| `dotnet new console -n Nama` | Membuat aplikasi console |
| `dotnet new winforms -n Nama` | Membuat aplikasi Windows Forms |
| `dotnet new wpf -n Nama` | Membuat aplikasi WPF |
| `dotnet build` | Mengompilasi proyek |
| `dotnet run` | Mengompilasi lalu menjalankan proyek |
| `dotnet add package NamaPaket` | Menambahkan paket NuGet |

## 5. Latihan: Hello World dengan .NET CLI

```powershell
dotnet new console -n HelloWorld
cd HelloWorld
dotnet run
```

Isi `Program.cs` hasil template (gaya *top-level statements*):

```csharp
Console.WriteLine("Hello, World!");
```

Versi lengkap dengan `namespace`, `class`, dan `Main` (gaya klasik) yang menunjukkan struktur program C#:

```csharp
using System;

namespace HelloWorld
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.Write("Masukkan nama: ");
            string nama = Console.ReadLine() ?? "Mahasiswa";
            Console.WriteLine($"Hello, {nama}! Selamat datang di PBKK.");
            Console.WriteLine($"Versi runtime .NET: {Environment.Version}");
        }
    }
}
```

### Dokumentasi Hasil Run

Dokumentasi hasil run Hello World ada di laporan [Minggu 02 — HelloWorld!](../Minggu-02/README.md#2-helloworld).

## 6. Kesimpulan

Framework mempercepat pengembangan dengan menyediakan struktur dan komponen siap pakai. .NET adalah framework serbaguna yang kini *open source* dan *cross-platform*; kode C# dikompilasi menjadi IL lalu dijalankan CLR. Dengan .NET CLI, proyek baru dapat dibuat, dikompilasi, dan dijalankan cukup dengan beberapa perintah, yang menjadi dasar untuk latihan WinForms dan WPF pada minggu berikutnya.
