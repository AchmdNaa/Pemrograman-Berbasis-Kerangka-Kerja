using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Text.Json;
using System.Threading.Tasks;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class MapViewModel : ObservableObject
{
    private readonly IReportService _service;
    private readonly INavigator _nav;
    private List<Report> _all = new();
    private bool _loading;

    [ObservableProperty] private string _pointsJson = "[]";
    [ObservableProperty] private string _searchText = string.Empty;
    [ObservableProperty] private string? _selectedCategory = "Semua";
    [ObservableProperty] private string _statusGroup = "Semua";
    [ObservableProperty] private Report? _selectedReport;
    [ObservableProperty] private int _activeCount;
    [ObservableProperty] private int _kritisCount;
    [ObservableProperty] private int _prosesCount;
    [ObservableProperty] private int _selesaiCount;

    public ObservableCollection<string> CategoryChips { get; } = new() { "Semua" };
    public ObservableCollection<Report> Nearby { get; } = new();

    /// <summary>Report the view should fly to once the map has finished loading.</summary>
    public int? PendingFocusId { get; set; }

    /// <summary>Raised when the user picks a report in the sidebar list.</summary>
    public event Action<int>? FocusRequested;

    public MapViewModel(IReportService service, INavigator nav)
    {
        _service = service;
        _nav = nav;
    }

    public async Task LoadAsync()
    {
        _loading = true;
        _all = await _service.GetAllReportsAsync();

        if (CategoryChips.Count <= 1)
        {
            foreach (var c in await _service.GetCategoriesAsync()) CategoryChips.Add(c.Name);
        }

        if (PendingFocusId.HasValue)
        {
            SelectedCategory = "Semua";
            StatusGroup = "Semua";
            SearchText = string.Empty;
        }
        _loading = false;
        Apply();

        if (PendingFocusId.HasValue)
            SelectedReport = Nearby.FirstOrDefault(r => r.Id == PendingFocusId.Value);
    }

    partial void OnSearchTextChanged(string value) => Apply();
    partial void OnSelectedCategoryChanged(string? value) => Apply();
    partial void OnStatusGroupChanged(string value) => Apply();

    partial void OnSelectedReportChanged(Report? value)
    {
        if (value != null) FocusRequested?.Invoke(value.Id);
    }

    private static bool IsProses(Report r) => r.Status is ReportStatus.Terverifikasi or ReportStatus.DalamPerbaikan;
    private static bool IsKritis(Report r) => r.Severity == SeverityLevel.Kritis && r.Status is not (ReportStatus.Selesai or ReportStatus.Ditolak);

    private void Apply()
    {
        if (_loading) return;

        IEnumerable<Report> q = _all;
        if (!string.IsNullOrEmpty(SelectedCategory) && SelectedCategory != "Semua")
            q = q.Where(r => r.Category?.Name == SelectedCategory);
        if (!string.IsNullOrWhiteSpace(SearchText))
        {
            var k = SearchText.Trim();
            q = q.Where(r => r.Title.Contains(k, StringComparison.OrdinalIgnoreCase)
                          || r.Address.Contains(k, StringComparison.OrdinalIgnoreCase)
                          || r.TicketCode.Contains(k, StringComparison.OrdinalIgnoreCase));
        }

        var baseSet = q.ToList();
        KritisCount = baseSet.Count(IsKritis);
        ProsesCount = baseSet.Count(r => IsProses(r) && !IsKritis(r));
        SelesaiCount = baseSet.Count(r => r.Status == ReportStatus.Selesai);

        IEnumerable<Report> filtered = StatusGroup switch
        {
            "Kritis" => baseSet.Where(IsKritis),
            "Proses" => baseSet.Where(r => IsProses(r) && !IsKritis(r)),
            "Selesai" => baseSet.Where(r => r.Status == ReportStatus.Selesai),
            _ => baseSet
        };

        var list = filtered.OrderByDescending(r => r.Status != ReportStatus.Selesai)
                           .ThenByDescending(r => r.Severity)
                           .ThenByDescending(r => r.CreatedAt)
                           .ToList();

        var selectedId = SelectedReport?.Id;
        Nearby.Clear();
        foreach (var r in list) Nearby.Add(r);
        ActiveCount = list.Count(r => r.Status is not (ReportStatus.Selesai or ReportStatus.Ditolak));

        PointsJson = JsonSerializer.Serialize(list.Select(r => new
        {
            id = r.Id,
            lat = r.Latitude,
            lng = r.Longitude,
            ticket = r.TicketCode,
            title = r.Title,
            address = r.Address,
            category = r.Category?.Name,
            severity = r.Severity.ToString(),
            status = Formatters.Label(r.Status),
            support = r.SupportCount,
            color = ColorFor(r)
        }));
    }

    private static string ColorFor(Report r)
    {
        if (r.Status == ReportStatus.Selesai) return "#16A34A";
        if (r.Status == ReportStatus.DalamPerbaikan) return "#0E7490";
        if (r.Severity == SeverityLevel.Kritis) return "#DC2626";
        return "#D97706";
    }

    [RelayCommand]
    private void SetStatusGroup(string group)
        => StatusGroup = StatusGroup == group ? "Semua" : group;

    [RelayCommand]
    private void NewReport() => _nav.Navigate("CreateReport");

    public void OpenReport(int id) => _nav.ShowReportDetail(id);

    public Report? Find(int id) => _all.FirstOrDefault(r => r.Id == id);
}
