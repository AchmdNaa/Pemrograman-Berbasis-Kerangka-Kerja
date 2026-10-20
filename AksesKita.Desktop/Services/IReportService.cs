using System.Collections.Generic;
using System.Threading.Tasks;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Services;

public interface IReportService
{
    Task<List<Report>> GetAllReportsAsync();
    Task<Report?> GetReportByIdAsync(int id);
    Task AddReportAsync(Report report);
    Task UpdateReportStatusAsync(int reportId, ReportStatus newStatus, string note, string updatedBy);
    Task<List<Category>> GetCategoriesAsync();
}
