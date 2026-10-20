using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Globalization;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class DashboardViewModel : ObservableObject
{
    private readonly IReportService _service;
    private readonly INavigator _nav;
    private List<Report> _periodReports = new();

    [ObservableProperty] private string _periodKey = "30";
    [ObservableProperty] private string _greeting = string.Empty;

    [ObservableProperty] private int _totalReports;
    [ObservableProperty] private string _totalDelta = string.Empty;
    [ObservableProperty] private int _inProgress;
    [ObservableProperty] private int _resolved;
    [ObservableProperty] private string _resolvedRate = string.Empty;
    [ObservableProperty] private int _urgentCount;
    [ObservableProperty] private string _urgentNote = string.Empty;
    [ObservableProperty] private string _closingEfficiency = string.Empty;

    public ObservableCollection<CategoryStat> Categories { get; } = new();
    public ObservableCollection<TrendBar> Trend { get; } = new();
    public ObservableCollection<PriorityItem> PriorityQueue { get; } = new();
    public ObservableCollection<ActivityItem> ActivityLog { get; } = new();

    public DashboardViewModel(IReportService service, INavigator nav)
    {
        _service = service;
        _nav = nav;
    }

    private int PeriodDays => PeriodKey switch { "7" => 7, "90" => 90, _ => 30 };

    public async Task LoadAsync()
    {
        Greeting = _nav.Session.IsAdmin
            ? $"Halo, {_nav.Session.UserName} — Pengawas Wilayah Kota Surabaya"
            : $"Halo, {_nav.Session.UserName} — Warga Peduli Kota Surabaya";

        var all = await _service.GetAllReportsAsync();
        var since = DateTime.Now.AddDays(-PeriodDays);
        var before = since.AddDays(-PeriodDays);

        _periodReports = all.Where(r => r.CreatedAt >= since).ToList();
        var previous = all.Count(r => r.CreatedAt >= before && r.CreatedAt < since);

        // Headline metrics for the selected period.
        TotalReports = _periodReports.Count;
        TotalDelta = previous == 0
            ? "periode sebelumnya belum ada data"
            : $"{(TotalReports - previous) * 100 / previous:+#;-#;0}% dibanding periode lalu";
        InProgress = _periodReports.Count(r => r.Status is ReportStatus.Terverifikasi or ReportStatus.DalamPerbaikan);
        Resolved = _periodReports.Count(r => r.Status == ReportStatus.Selesai);
        ResolvedRate = TotalReports == 0 ? "belum ada laporan" : $"{Resolved * 100 / TotalReports}% tingkat pemulihan";
        var urgent = _periodReports.Where(r => r.Severity == SeverityLevel.Kritis
                                               && r.Status is not (ReportStatus.Selesai or ReportStatus.Ditolak)).ToList();
        UrgentCount = urgent.Count;
        UrgentNote = $"{urgent.Count(r => r.Status == ReportStatus.Baru)} belum diverifikasi";
        ClosingEfficiency = TotalReports == 0 ? "-" : $"{Resolved * 100.0 / TotalReports:0.0}%";

        BuildCategories();
        BuildTrend(all);
        BuildPriority(all);
        await BuildActivityAsync(all);
    }

    private void BuildCategories()
    {
        Categories.Clear();
        var total = _periodReports.Count == 0 ? 1 : _periodReports.Count;
        var palette = new[] { "#1D4E89", "#0E7490", "#6366F1", "#38BDF8", "#F59E0B", "#14B8A6" };
        var grouped = _periodReports
            .GroupBy(r => r.Category?.Name ?? "Lainnya")
            .Select(g => new { Name = g.Key, Count = g.Count() })
            .OrderByDescending(g => g.Count)
            .ToList();
        for (int i = 0; i < grouped.Count; i++)
            Categories.Add(new CategoryStat(grouped[i].Name, grouped[i].Count * 100 / total, grouped[i].Count, palette[i % palette.Length]));
    }

    private void BuildTrend(List<Report> all)
    {
        Trend.Clear();
        var months = Enumerable.Range(0, 6)
            .Select(i => new DateTime(DateTime.Now.Year, DateTime.Now.Month, 1).AddMonths(-(5 - i)))
            .ToList();
        var rows = months.Select(m => new
        {
            Label = m.ToString("MMM", new CultureInfo("id-ID")),
            Incoming = all.Count(r => r.CreatedAt.Year == m.Year && r.CreatedAt.Month == m.Month),
            Done = all.Count(r => r.CreatedAt.Year == m.Year && r.CreatedAt.Month == m.Month && r.Status == ReportStatus.Selesai)
        }).ToList();
        var max = Math.Max(1, rows.Max(r => r.Incoming));
        foreach (var r in rows)
            Trend.Add(new TrendBar(r.Label, r.Incoming, r.Done, r.Incoming * 150.0 / max, r.Done * 150.0 / max));
    }

    private void BuildPriority(List<Report> all)
    {
        PriorityQueue.Clear();
        foreach (var r in all.Where(r => r.Severity == SeverityLevel.Kritis && r.Status is not (ReportStatus.Selesai or ReportStatus.Ditolak))
                             .OrderByDescending(r => r.CreatedAt).Take(4))
            PriorityQueue.Add(new PriorityItem(r.Id, r.TicketCode, r.Title, r.Address,
                Formatters.Ago(r.CreatedAt), r.IsAnonymous ? "Anonim" : (r.ReporterName ?? "Warga")));
    }

    private async Task BuildActivityAsync(List<Report> all)
    {
        ActivityLog.Clear();
        var logs = await _service.GetRecentLogsAsync(5);
        foreach (var l in logs)
        {
            var report = all.FirstOrDefault(r => r.Id == l.ReportId);
            ActivityLog.Add(new ActivityItem(
                $"{report?.TicketCode} — {Formatters.Label(l.NewStatus)}",
                $"{l.UpdatedBy}: {(string.IsNullOrWhiteSpace(l.Note) ? report?.Title : l.Note)}",
                Formatters.Ago(l.CreatedAt),
                report?.Id ?? 0,
                Formatters.StatusColor(l.NewStatus)));
        }
    }

    [RelayCommand]
    private async Task SetPeriod(string key)
    {
        PeriodKey = key;
        await LoadAsync();
    }

    [RelayCommand]
    private void OpenReport(int id)
    {
        if (id > 0) _nav.ShowReportDetail(id);
    }

    [RelayCommand]
    private void OpenQueue() => _nav.Navigate("ReportList");

    [RelayCommand]
    private void ExportCsv()
    {
        var dlg = new Microsoft.Win32.SaveFileDialog
        {
            Filter = "CSV (*.csv)|*.csv",
            FileName = $"rekap-aksesibilitas-surabaya-{DateTime.Now:yyyyMMdd}.csv"
        };
        if (dlg.ShowDialog() != true) return;

        static string Q(string? s) => "\"" + (s ?? string.Empty).Replace("\"", "\"\"") + "\"";
        var sb = new StringBuilder();
        sb.AppendLine("Tiket,Judul,Kategori,Tingkat,Status,Alamat,Latitude,Longitude,Dukungan,Dilaporkan");
        foreach (var r in _periodReports)
            sb.AppendLine(string.Join(",", Q(r.TicketCode), Q(r.Title), Q(r.Category?.Name), r.Severity, Q(Formatters.Label(r.Status)),
                Q(r.Address), r.Latitude.ToString(CultureInfo.InvariantCulture), r.Longitude.ToString(CultureInfo.InvariantCulture),
                r.SupportCount, r.CreatedAt.ToString("yyyy-MM-dd HH:mm")));
        File.WriteAllText(dlg.FileName, sb.ToString(), new UTF8Encoding(true));
        MessageBox.Show($"Rekap {_periodReports.Count} laporan berhasil diekspor.", "Ekspor CSV",
            MessageBoxButton.OK, MessageBoxImage.Information);
    }
}

public record CategoryStat(string Name, int Percent, int Count, string Color)
{
    // Lebar batang mengikuti lebar kartu: bagian terisi dan sisa dibagi sebagai kolom bintang.
    public GridLength BarFill => new(Percent, GridUnitType.Star);
    public GridLength BarRest => new(100 - Percent, GridUnitType.Star);
}

public record TrendBar(string Month, int Incoming, int Done, double IncomingHeight, double ResolvedHeight);

public record PriorityItem(int Id, string Code, string Title, string Location, string Time, string Reporter);

public record ActivityItem(string Title, string Detail, string Time, int ReportId, string Color);
