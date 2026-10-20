using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.EntityFrameworkCore;
using AksesKita.Desktop.Data;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Services;

public class ReportService : IReportService
{
    // Setiap operasi memakai DbContext baru agar aman saat beberapa query berjalan bersamaan
    // (misalnya pengguna mengetik cepat di kolom pencarian).
    private readonly Func<AksesKitaDbContext> _dbFactory;

    public ReportService(Func<AksesKitaDbContext> dbFactory)
    {
        _dbFactory = dbFactory;
    }

    public async Task<List<Report>> GetAllReportsAsync(string? keyword = null, ReportStatus? status = null)
    {
        using var db = _dbFactory();
        IQueryable<Report> query = db.Reports.Include(r => r.Category);

        if (!string.IsNullOrWhiteSpace(keyword))
        {
            var k = keyword.Trim();
            query = query.Where(r => r.Title.Contains(k)
                                  || r.TicketCode.Contains(k)
                                  || r.Address.Contains(k));
        }

        if (status.HasValue)
        {
            query = query.Where(r => r.Status == status.Value);
        }

        return await query.OrderByDescending(r => r.CreatedAt).ToListAsync();
    }

    public async Task<Report?> GetReportByIdAsync(int id)
    {
        using var db = _dbFactory();
        return await db.Reports
            .Include(r => r.Category)
            .Include(r => r.StatusLogs)
            .FirstOrDefaultAsync(r => r.Id == id);
    }

    public async Task AddReportAsync(Report report)
    {
        Validate(report);

        using var db = _dbFactory();

        if (report.CategoryId == 0)
        {
            report.CategoryId = await db.Categories.Select(c => c.Id).FirstAsync();
        }

        report.Category = null; // cukup CategoryId; hindari EF menyisipkan kategori ganda
        report.TicketCode = await GenerateTicketCodeAsync(db);
        report.Status = ReportStatus.Baru;
        report.CreatedAt = DateTime.Now;
        report.StatusLogs.Add(new ReportStatusLog
        {
            OldStatus = ReportStatus.Baru,
            NewStatus = ReportStatus.Baru,
            Note = "Laporan dibuat",
            UpdatedBy = report.IsAnonymous ? "Anonim" : (report.ReporterName ?? "Petugas"),
            CreatedAt = DateTime.Now
        });

        db.Reports.Add(report);
        await db.SaveChangesAsync();
    }

    public async Task DeleteReportAsync(int reportId)
    {
        using var db = _dbFactory();
        var report = await db.Reports.Include(r => r.StatusLogs).FirstOrDefaultAsync(r => r.Id == reportId);
        if (report == null) return;

        db.Reports.Remove(report);
        await db.SaveChangesAsync();
    }

    public async Task UpdateReportStatusAsync(int reportId, ReportStatus newStatus, string note, string updatedBy)
    {
        using var db = _dbFactory();
        var report = await db.Reports.Include(r => r.StatusLogs).FirstOrDefaultAsync(r => r.Id == reportId);
        if (report == null) return;

        if (newStatus == ReportStatus.Ditolak && string.IsNullOrWhiteSpace(note))
        {
            throw new InvalidOperationException("Catatan wajib diisi bila laporan ditolak.");
        }

        var oldStatus = report.Status;
        report.Status = newStatus;
        report.StatusLogs.Add(new ReportStatusLog
        {
            ReportId = reportId,
            OldStatus = oldStatus,
            NewStatus = newStatus,
            Note = note,
            UpdatedBy = updatedBy,
            CreatedAt = DateTime.Now
        });
        await db.SaveChangesAsync();
    }

    public async Task<List<Category>> GetCategoriesAsync()
    {
        using var db = _dbFactory();
        return await db.Categories.OrderBy(c => c.Id).ToListAsync();
    }

    public async Task<DashboardSummary> GetDashboardSummaryAsync()
    {
        using var db = _dbFactory();
        var total = await db.Reports.CountAsync();
        var inProgress = await db.Reports.CountAsync(r => r.Status == ReportStatus.DalamPerbaikan);
        var resolved = await db.Reports.CountAsync(r => r.Status == ReportStatus.Selesai);
        var urgent = await db.Reports.CountAsync(r => r.Severity == SeverityLevel.Kritis
                                                  && r.Status == ReportStatus.Baru);
        return new DashboardSummary(total, inProgress, resolved, urgent);
    }

    // Aturan validasi sesuai PRD bagian 5.2
    private static void Validate(Report r)
    {
        var title = r.Title?.Trim() ?? string.Empty;
        var description = r.Description?.Trim() ?? string.Empty;

        if (title.Length < 5 || title.Length > 100)
            throw new InvalidOperationException("Judul wajib diisi, 5–100 karakter.");
        if (description.Length < 20)
            throw new InvalidOperationException("Deskripsi wajib diisi, minimal 20 karakter.");
        if (string.IsNullOrWhiteSpace(r.Address))
            throw new InvalidOperationException("Alamat / patokan lokasi wajib diisi.");
        if (r.Latitude < -90 || r.Latitude > 90 || r.Longitude < -180 || r.Longitude > 180)
            throw new InvalidOperationException("Koordinat tidak valid.");

        r.Title = title;
        r.Description = description;
        r.Address = r.Address.Trim();
    }

    // Contoh kode: AK-20261021-0001
    private static async Task<string> GenerateTicketCodeAsync(AksesKitaDbContext db)
    {
        var prefix = $"AK-{DateTime.Now:yyyyMMdd}-";
        var countToday = await db.Reports.CountAsync(r => r.TicketCode.StartsWith(prefix));
        return $"{prefix}{countToday + 1:D4}";
    }
}
