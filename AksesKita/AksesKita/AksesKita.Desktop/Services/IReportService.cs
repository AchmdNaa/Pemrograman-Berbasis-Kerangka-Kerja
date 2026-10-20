using System.Collections.Generic;
using System.Threading.Tasks;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Services;

public interface IReportService
{
    Task<List<Report>> GetAllReportsAsync();
    Task<Report?> GetReportByIdAsync(int id);
    Task<Report> AddReportAsync(Report report);
    Task UpdateReportAsync(Report edited);
    Task DeleteReportAsync(int id);
    Task UpdateReportStatusAsync(int reportId, ReportStatus newStatus, string note, string updatedBy);
    Task AddSupportAsync(int reportId);
    Task<List<Category>> GetCategoriesAsync();
    Task<List<ReportStatusLog>> GetRecentLogsAsync(int take);
}
