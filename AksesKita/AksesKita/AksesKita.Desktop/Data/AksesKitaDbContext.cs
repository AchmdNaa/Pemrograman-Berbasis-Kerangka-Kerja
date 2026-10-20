using System;
using System.IO;
using System.Linq;
using Microsoft.Data.Sqlite;
using Microsoft.EntityFrameworkCore;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Security;

namespace AksesKita.Desktop.Data;

public class AksesKitaDbContext : DbContext
{
    public DbSet<Report> Reports => Set<Report>();
    public DbSet<Category> Categories => Set<Category>();
    public DbSet<ReportStatusLog> ReportStatusLogs => Set<ReportStatusLog>();
    public DbSet<AppUser> Users => Set<AppUser>();

    // Store the database next to the executable so it is stable across runs.
    private static readonly string DbPath =
        Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "akseskita.db");

    // Bump this when the schema or the seed data changes; the dev database is rebuilt once.
    private const string SchemaVersion = "v6-auth";

    protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
    {
        optionsBuilder.UseSqlite($"Data Source={DbPath}");
    }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        // Image uploads are stored as a single file path (Report.ImagePath).
        modelBuilder.Entity<Report>().Ignore(r => r.ImageUrls);

        // Satu email hanya boleh dipakai satu akun.
        modelBuilder.Entity<AppUser>().HasIndex(u => u.Email).IsUnique();
    }

    /// <summary>Creates the database on first run and fills it with starter data for Surabaya.</summary>
    public static void Initialize()
    {
        var marker = DbPath + "." + SchemaVersion;
        if (!File.Exists(marker))
        {
            SqliteConnection.ClearAllPools();
            foreach (var f in new[] { DbPath, DbPath + "-wal", DbPath + "-shm", DbPath + "-journal" })
            {
                try { if (File.Exists(f)) File.Delete(f); } catch { /* best effort */ }
            }
            File.WriteAllText(marker, SchemaVersion);
        }

        using var db = new AksesKitaDbContext();
        db.Database.EnsureCreated();

        if (!db.Categories.Any())
        {
            db.Categories.AddRange(
                new Category { Name = "Trotoar & Jalur", Icon = "🚶", Description = "Permukaan rusak, lubang, atau okupasi PKL/parkir liar." },
                new Category { Name = "Ramp Kursi Roda", Icon = "♿", Description = "Terlalu curam, terhalang portal, atau permukaan licin." },
                new Category { Name = "Guiding Block", Icon = "⠿", Description = "Ubin pemandu patah, hilang arah, menabrak pohon/tiang." },
                new Category { Name = "Lift & Eskalator", Icon = "🛗", Description = "Mati total, tombol braille rusak, atau lift terkunci." },
                new Category { Name = "Toilet Disabilitas", Icon = "🚻", Description = "Pintu sempit, pegangan rubuh, atau dijadikan gudang." },
                new Category { Name = "Halte & Transit", Icon = "🚌", Description = "Gap peron tinggi, audio announcer bus/KRL tidak berfungsi." }
            );
            db.SaveChanges();
        }

        if (!db.Reports.Any())
        {
            SeedReports(db);
        }

        if (!db.Users.Any())
        {
            SeedUsers(db);
        }
    }

    /// <summary>Akun demo. Kata sandi disimpan sebagai hash BCrypt, bukan teks asli.</summary>
    private static void SeedUsers(AksesKitaDbContext db)
    {
        db.Users.AddRange(
            new AppUser { Name = "Petugas Dinas PU", Email = "admin@akseskita.id", PasswordHash = PasswordHasher.Hash("Admin2026"), Role = UserRole.Admin },
            new AppUser { Name = "Warga Peduli", Email = "warga@akseskita.id", PasswordHash = PasswordHasher.Hash("Warga2026"), Role = UserRole.Pelapor });
        db.SaveChanges();
    }

    private static void SeedReports(AksesKitaDbContext db)
    {
        var cats = db.Categories.ToList();
        int C(string prefix) => cats.First(c => c.Name.StartsWith(prefix)).Id;
        var now = DateTime.Now;

        Report R(string cat, string title, string desc, SeverityLevel sev, ReportStatus status,
                 string address, double lat, double lng, string reporter, bool anon, int support, double hoursAgo)
            => new()
            {
                CategoryId = C(cat), Title = title, Description = desc, Severity = sev, Status = status,
                Address = address, Latitude = lat, Longitude = lng, ReporterName = reporter,
                IsAnonymous = anon, SupportCount = support, CreatedAt = now.AddHours(-hoursAgo)
            };

        var seeds = new[]
        {
            R("Ramp", "Ramp Halte Suroboyo Bus Terlalu Curam", "Kemiringan ramp melebihi standar aman dan sangat licin saat hujan; pengguna kursi roda tidak bisa naik tanpa bantuan orang lain.", SeverityLevel.Kritis, ReportStatus.Baru, "Halte Suroboyo Bus, Jl. Basuki Rahmat (depan Tunjungan Plaza)", -7.2625, 112.7403, "Rani W.", false, 64, 0.4),
            R("Lift", "Lift Penyeberangan Stasiun Gubeng Mati Total", "Lift menuju jembatan penyeberangan tidak berfungsi dan terkunci tanpa petugas; lansia dan pengguna kursi roda terpaksa memutar jauh.", SeverityLevel.Kritis, ReportStatus.Terverifikasi, "Stasiun Gubeng, Jl. Gubeng Masjid", -7.2652, 112.7524, "Komunitas Difabel Surabaya", false, 120, 3),
            R("Guiding", "Guiding Block Terputus Lubang Galian", "Ubin pemandu terputus sepanjang ±4 meter akibat galian utilitas yang belum ditutup kembali; sangat berbahaya bagi tunanetra.", SeverityLevel.Kritis, ReportStatus.DalamPerbaikan, "Jl. Tunjungan (depan Hotel Majapahit)", -7.2603, 112.7380, "Anonim", true, 89, 26),
            R("Trotoar", "Trotoar Amblas di Depan Balai Kota", "Permukaan trotoar amblas membentuk cekungan 15–20 cm tanpa rambu peringatan.", SeverityLevel.Sedang, ReportStatus.Selesai, "Jl. Taman Surya, Balai Kota Surabaya", -7.2587, 112.7508, "Budi S.", false, 41, 150),
            R("Toilet", "Toilet Difabel Dikunci dan Dijadikan Gudang", "Toilet khusus difabel di lantai dasar dikunci dan dipakai menyimpan alat kebersihan.", SeverityLevel.Sedang, ReportStatus.Terverifikasi, "Terminal Joyoboyo, Jl. Joyoboyo", -7.2994, 112.7387, "Dimas P.", false, 31, 28),
            R("Halte", "Celah Peron Halte Terlalu Tinggi", "Celah antara bus dan peron terlalu tinggi sehingga kursi roda tidak dapat naik tanpa bantuan.", SeverityLevel.Sedang, ReportStatus.Baru, "Halte Jl. Ahmad Yani (depan Royal Plaza)", -7.3077, 112.7342, "Sari L.", false, 70, 8),
            R("Trotoar", "Trotoar Dipenuhi PKL dan Parkir Liar", "Jalur pedestrian tertutup lapak dan parkir motor sehingga pengguna kursi roda harus turun ke badan jalan.", SeverityLevel.Sedang, ReportStatus.Baru, "Jl. Pemuda (sekitar Delta Plaza)", -7.2631, 112.7468, "Dimas P.", false, 33, 800),
            R("Ramp", "Ramp Pintu Masuk Kampus Terhalang Pot", "Ramp menuju gedung dihalangi pot besar dan kabel yang melintang.", SeverityLevel.Rendah, ReportStatus.Selesai, "Kampus B Universitas Airlangga, Jl. Dharmawangsa", -7.2699, 112.7578, "Mahasiswa Unair", false, 12, 120),
            R("Guiding", "Ubin Pemandu Terlepas di Taman Bungkul", "Ubin kuning di sekitar taman terlepas dan menimbulkan risiko tersandung.", SeverityLevel.Sedang, ReportStatus.Selesai, "Taman Bungkul, Jl. Raya Darmo", -7.2913, 112.7396, "Anonim", true, 27, 2100),
            R("Lift", "Lift JPO Rusak Sudah 3 Pekan", "Lift jembatan penyeberangan tidak berfungsi selama tiga pekan; pengguna kursi roda tidak bisa menyeberang.", SeverityLevel.Kritis, ReportStatus.DalamPerbaikan, "JPO Jl. Ahmad Yani (depan Stasiun Wonokromo)", -7.3029, 112.7378, "Komunitas Difabel Surabaya", false, 98, 90),
            R("Trotoar", "Trotoar Berlubang di Kawasan Kampus ITS", "Beberapa lubang pada trotoar mengganggu pengguna tongkat putih.", SeverityLevel.Rendah, ReportStatus.Selesai, "Kampus ITS Sukolilo, Jl. Raya ITS", -7.2819, 112.7948, "Fajar R.", false, 9, 2900),
            R("Halte", "Pengumuman Audio Stasiun Tidak Berfungsi", "Audio announcer tidak berbunyi sehingga tunanetra kesulitan mengetahui kedatangan kereta.", SeverityLevel.Sedang, ReportStatus.Selesai, "Stasiun Pasar Turi, Jl. Semarang", -7.2460, 112.7295, "Anonim", true, 22, 3600),
            R("Toilet", "Pintu Toilet Disabilitas Terlalu Sempit", "Lebar pintu kurang dari 80 cm sehingga kursi roda tidak dapat masuk.", SeverityLevel.Sedang, ReportStatus.Terverifikasi, "RSUD Dr. Soetomo, Jl. Mayjen Prof. Dr. Moestopo", -7.2689, 112.7584, "Sari L.", false, 38, 1700),
            R("Ramp", "Ramp Jembatan Merah Plaza Licin Tanpa Pegangan", "Tidak ada handrail pada ramp dan permukaan sangat licin saat basah.", SeverityLevel.Kritis, ReportStatus.Baru, "Jembatan Merah Plaza, Jl. Jembatan Merah", -7.2332, 112.7366, "Heru K.", false, 45, 5),
            R("Guiding", "Guiding Block Hilang di Area Kenjeran", "Ubin pemandu tidak ada di sepanjang jalur pedestrian menuju pintu masuk wisata.", SeverityLevel.Rendah, ReportStatus.Baru, "Taman Hiburan Pantai Kenjeran", -7.2398, 112.7954, "Anonim", true, 6, 40),
            R("Lift", "Eskalator Mati dan Lift Terkunci di Mal", "Eskalator mati dan lift terkunci sehingga lantai atas tidak dapat diakses pengguna kursi roda.", SeverityLevel.Sedang, ReportStatus.DalamPerbaikan, "Galaxy Mall, Jl. Dharmahusada Indah Timur", -7.2759, 112.7803, "Hana T.", false, 52, 1900),
            R("Trotoar", "Trotoar Terputus Tiang Listrik", "Tiang listrik di tengah trotoar menyisakan lebar kurang dari 60 cm sehingga kursi roda tidak lewat.", SeverityLevel.Sedang, ReportStatus.Baru, "Jl. Raya Darmo (dekat Taman Bungkul sisi selatan)", -7.2940, 112.7400, "Budi S.", false, 18, 12),
            R("Halte", "Halte Tanpa Jalur Ramp", "Halte hanya bisa diakses lewat tangga sehingga tidak dapat dipakai pengguna kursi roda.", SeverityLevel.Rendah, ReportStatus.Selesai, "Halte Monumen Kapal Selam, Jl. Pemuda", -7.2654, 112.7503, "Mahasiswa Unair", false, 15, 1300),
        };

        db.Reports.AddRange(seeds);
        db.SaveChanges();

        // Ticket code depends on the generated id.
        foreach (var r in seeds) r.TicketCode = $"#AKS-{r.CreatedAt:yyyy}-{r.Id:D4}";
        db.SaveChanges();

        // Simple handling history so the timeline and activity log have real content.
        foreach (var r in seeds.Where(s => s.Status != ReportStatus.Baru))
        {
            void Log(ReportStatus from, ReportStatus to, string note, double hoursAfter)
            {
                var at = r.CreatedAt.AddHours(hoursAfter);
                if (at > now) at = now.AddMinutes(-5);
                db.ReportStatusLogs.Add(new ReportStatusLog
                {
                    ReportId = r.Id, OldStatus = from, NewStatus = to,
                    Note = note, UpdatedBy = "Petugas DPU Bina Marga", CreatedAt = at
                });
            }

            Log(ReportStatus.Baru, ReportStatus.Terverifikasi, "Laporan diverifikasi valid melalui cross-check foto dan titik lokasi.", 2);
            if (r.Status is ReportStatus.DalamPerbaikan or ReportStatus.Selesai)
                Log(ReportStatus.Terverifikasi, ReportStatus.DalamPerbaikan, "Regu teknis diterjunkan ke lokasi untuk pengerjaan perbaikan.", 8);
            if (r.Status == ReportStatus.Selesai)
                Log(ReportStatus.DalamPerbaikan, ReportStatus.Selesai, "Perbaikan selesai dan telah divalidasi di lapangan.", 40);
        }
        db.SaveChanges();
    }
}
