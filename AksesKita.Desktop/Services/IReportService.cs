using System.Collections.Generic;
using System.Threading.Tasks;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Services;

public interface IReportService
{
    /// <summary>Ambil laporan, bisa dicari berdasarkan kata kunci (kode, judul, alamat) dan status.</summary>
    Task<List<Report>> GetAllReportsAsync(string? keyword = null, ReportStatus? status = null);
    Task<Report?> GetReportByIdAsync(int id);

    /// <summary>Simpan laporan baru. Melempar InvalidOperationException bila data tidak valid.</summary>
    Task AddReportAsync(Report report);
    Task DeleteReportAsync(int reportId);
    Task UpdateReportStatusAsync(int reportId, ReportStatus newStatus, string note, string updatedBy);

    Task<List<Category>> GetCategoriesAsync();
    Task<DashboardSummary> GetDashboardSummaryAsync();
}
