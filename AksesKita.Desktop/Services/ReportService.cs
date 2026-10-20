using System;
using System.Collections.Generic;
using System.Threading.Tasks;
using Microsoft.EntityFrameworkCore;
using AksesKita.Desktop.Data;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Services;

public class ReportService : IReportService
{
    private readonly AksesKitaDbContext _db;

    public ReportService(AksesKitaDbContext db)
    {
        _db = db;
        _db.Database.EnsureCreated();
    }

    public async Task<List<Report>> GetAllReportsAsync()
    {
        return await _db.Reports.Include(r => r.Category).Include(r => r.StatusLogs).ToListAsync();
    }

    public async Task<Report?> GetReportByIdAsync(int id)
    {
        return await _db.Reports.Include(r => r.Category).Include(r => r.StatusLogs).FirstOrDefaultAsync(r => r.Id == id);
    }

    public async Task AddReportAsync(Report report)
    {
        _db.Reports.Add(report);
        await _db.SaveChangesAsync();
    }

    public async Task UpdateReportStatusAsync(int reportId, ReportStatus newStatus, string note, string updatedBy)
    {
        var report = await _db.Reports.Include(r => r.StatusLogs).FirstOrDefaultAsync(r => r.Id == reportId);
        if (report != null)
        {
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
            await _db.SaveChangesAsync();
        }
    }

    public async Task<List<Category>> GetCategoriesAsync()
    {
        return await _db.Categories.ToListAsync();
    }
}
