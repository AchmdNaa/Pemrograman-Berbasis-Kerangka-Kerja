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
        optionsBuilder.UseSqlite("Data Source=akseskita.db");
    }
}
