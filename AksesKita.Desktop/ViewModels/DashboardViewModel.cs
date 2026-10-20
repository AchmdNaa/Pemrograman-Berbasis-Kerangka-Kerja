using System.Collections.ObjectModel;
using System.Linq;
using System.Threading.Tasks;
using CommunityToolkit.Mvvm.ComponentModel;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class DashboardViewModel : ObservableObject
{
    private readonly IReportService _service;

    [ObservableProperty]
    private int _totalReports;

    [ObservableProperty]
    private int _inProgress;

    [ObservableProperty]
    private int _resolved;

    [ObservableProperty]
    private int _urgentCount;

    // Aktivitas terbaru: 5 laporan terakhir
    public ObservableCollection<Report> RecentReports { get; } = new();

    public DashboardViewModel(IReportService service)
    {
        _service = service;
    }

    // Semua angka dihitung dari database, bukan angka tetap
    public async Task LoadAsync()
    {
        var summary = await _service.GetDashboardSummaryAsync();
        TotalReports = summary.Total;
        InProgress = summary.InProgress;
        Resolved = summary.Resolved;
        UrgentCount = summary.Urgent;

        var recent = await _service.GetAllReportsAsync();
        RecentReports.Clear();
        foreach (var report in recent.Take(5))
        {
            RecentReports.Add(report);
        }
    }
}
