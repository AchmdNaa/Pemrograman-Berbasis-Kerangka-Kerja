using Microsoft.EntityFrameworkCore;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Data;

public class AksesKitaDbContext : DbContext
{
    public DbSet<Report> Reports => Set<Report>();
    public DbSet<Category> Categories => Set<Category>();
    public DbSet<ReportStatusLog> ReportStatusLogs => Set<ReportStatusLog>();

    protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
    {
        // Database disimpan di folder data pengguna agar tidak hilang saat aplikasi dibuild ulang
        var folder = Path.Combine(
            Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
            "AksesKita");
        Directory.CreateDirectory(folder);
        optionsBuilder.UseSqlite($"Data Source={Path.Combine(folder, "akseskita.db")}");
    }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        // Simpan enum sebagai teks supaya isi database mudah dibaca
        modelBuilder.Entity<Report>().Property(r => r.Severity).HasConversion<string>();
        modelBuilder.Entity<Report>().Property(r => r.Status).HasConversion<string>();
        modelBuilder.Entity<ReportStatusLog>().Property(l => l.OldStatus).HasConversion<string>();
        modelBuilder.Entity<ReportStatusLog>().Property(l => l.NewStatus).HasConversion<string>();

        modelBuilder.Entity<Report>().HasIndex(r => r.TicketCode).IsUnique();

        // Kategori awal (sesuai PRD bagian 5.1)
        modelBuilder.Entity<Category>().HasData(
            new Category { Id = 1, Name = "Jalur Pejalan Kaki / Trotoar", Icon = "🚶", Description = "Trotoar rusak, tanpa ramp, atau terhalang" },
            new Category { Id = 2, Name = "Guiding Block", Icon = "🟨", Description = "Jalur pemandu tunanetra tidak ada, terputus, atau terhalang" },
            new Category { Id = 3, Name = "Pintu & Akses Masuk Bangunan", Icon = "🚪", Description = "Tangga tanpa alternatif ramp atau pintu terlalu sempit" },
            new Category { Id = 4, Name = "Lift & Eskalator", Icon = "🛗", Description = "Lift rusak atau tidak tersedia" },
            new Category { Id = 5, Name = "Toilet Aksesibel", Icon = "🚻", Description = "Toilet tidak ramah kursi roda" },
            new Category { Id = 6, Name = "Transportasi Publik", Icon = "🚌", Description = "Halte, stasiun, atau kendaraan sulit diakses" },
            new Category { Id = 7, Name = "Parkir Difabel", Icon = "♿", Description = "Parkir difabel tidak ada atau disalahgunakan" },
            new Category { Id = 8, Name = "Rambu & Informasi", Icon = "🪧", Description = "Rambu atau informasi tidak terbaca" },
            new Category { Id = 9, Name = "Lainnya", Icon = "📌", Description = "Hambatan lain" });
    }
}
