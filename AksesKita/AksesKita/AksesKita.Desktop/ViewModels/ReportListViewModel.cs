using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Threading.Tasks;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class ReportListViewModel : ObservableObject
{
    private readonly IReportService _service;
    private readonly INavigator _nav;
    private const int PageSize = 10;
    private List<Report> _all = new();
    private List<Report> _filtered = new();

    [ObservableProperty] private ObservableCollection<Report> _reports = new();
    [ObservableProperty] private string _searchKeyword = string.Empty;
    [ObservableProperty] private string _statusFilter = "Semua";
    [ObservableProperty] private string _severityFilter = "Semua";
    [ObservableProperty] private bool _onlyMine;
    [ObservableProperty] private bool _isAdmin;
    [ObservableProperty] private string _summary = string.Empty;
    [ObservableProperty, NotifyPropertyChangedFor(nameof(PageText)), NotifyPropertyChangedFor(nameof(CanGoPrevious)), NotifyPropertyChangedFor(nameof(CanGoNext))]
    private int _currentPage = 1;
    [ObservableProperty, NotifyPropertyChangedFor(nameof(PageText)), NotifyPropertyChangedFor(nameof(CanGoNext))]
    private int _totalPages = 1;

    public string PageText => $"Halaman {CurrentPage} dari {TotalPages}";
    public bool CanGoPrevious => CurrentPage > 1;
    public bool CanGoNext => CurrentPage < TotalPages;

    public string[] StatusFilters { get; } = { "Semua", "Baru", "Terverifikasi", "Dalam Perbaikan", "Selesai", "Ditolak" };
    public string[] SeverityFilters { get; } = { "Semua", "Kritis", "Sedang", "Rendah" };

    public ReportListViewModel(IReportService service, INavigator nav)
    {
        _service = service;
        _nav = nav;
    }

    public async Task LoadAsync()
    {
        IsAdmin = _nav.Session.IsAdmin;
        _all = await _service.GetAllReportsAsync();
        Apply(resetPage: false);
    }

    partial void OnSearchKeywordChanged(string value) => Apply();
    partial void OnStatusFilterChanged(string value) => Apply();
    partial void OnSeverityFilterChanged(string value) => Apply();
    partial void OnOnlyMineChanged(bool value) => Apply();

    /// <param name="resetPage">true bila filter berubah (kembali ke halaman 1); false saat memuat ulang atau berpindah halaman.</param>
    private void Apply(bool resetPage = true)
    {
        IEnumerable<Report> q = _all;

        if (!string.IsNullOrWhiteSpace(SearchKeyword))
        {
            var k = SearchKeyword.Trim();
            q = q.Where(r => Contains(r.Title, k) || Contains(r.Address, k) || Contains(r.TicketCode, k)
                          || Contains(r.Description, k) || Contains(r.Category?.Name, k));
        }
        if (StatusFilter != "Semua" && Enum.TryParse<ReportStatus>(StatusFilter.Replace(" ", ""), out var st))
            q = q.Where(r => r.Status == st);
        if (SeverityFilter != "Semua" && Enum.TryParse<SeverityLevel>(SeverityFilter, out var sv))
            q = q.Where(r => r.Severity == sv);
        if (OnlyMine)
            q = q.Where(r => r.ReporterName == _nav.Session.UserName);

        _filtered = q.ToList();
        if (resetPage) CurrentPage = 1;
        ShowPage();
    }

    /// <summary>Menampilkan satu halaman dari hasil filter (10 laporan per halaman).</summary>
    private void ShowPage()
    {
        TotalPages = System.Math.Max(1, (int)System.Math.Ceiling(_filtered.Count / (double)PageSize));
        if (CurrentPage > TotalPages) CurrentPage = TotalPages;
        if (CurrentPage < 1) CurrentPage = 1;

        var pageItems = _filtered.Skip((CurrentPage - 1) * PageSize).Take(PageSize).ToList();
        Reports = new ObservableCollection<Report>(pageItems);

        Summary = _filtered.Count == 0
            ? $"Tidak ada laporan yang cocok. Total {_all.Count} laporan."
            : $"Menampilkan {(CurrentPage - 1) * PageSize + 1}–{(CurrentPage - 1) * PageSize + pageItems.Count} dari {_filtered.Count} laporan (total {_all.Count})";
    }

    [RelayCommand]
    private void NextPage()
    {
        if (!CanGoNext) return;
        CurrentPage++;
        ShowPage();
    }

    [RelayCommand]
    private void PreviousPage()
    {
        if (!CanGoPrevious) return;
        CurrentPage--;
        ShowPage();
    }

    private static bool Contains(string? text, string keyword)
        => text != null && text.Contains(keyword, StringComparison.OrdinalIgnoreCase);

    [RelayCommand]
    private void ResetFilter()
    {
        SearchKeyword = string.Empty;
        StatusFilter = "Semua";
        SeverityFilter = "Semua";
        OnlyMine = false;
    }

    [RelayCommand]
    private void OpenDetail(Report? report)
    {
        if (report != null) _nav.ShowReportDetail(report.Id);
    }

    [RelayCommand]
    private void NewReport() => _nav.Navigate("CreateReport");

    [RelayCommand]
    private async Task DeleteReport(Report? report)
    {
        if (report == null) return;
        var ok = System.Windows.MessageBox.Show(
            $"Hapus laporan \"{report.Title}\"?", "Konfirmasi Hapus",
            System.Windows.MessageBoxButton.YesNo, System.Windows.MessageBoxImage.Warning);
        if (ok == System.Windows.MessageBoxResult.Yes)
        {
            await _service.DeleteReportAsync(report.Id);
            _all.RemoveAll(r => r.Id == report.Id);
            Apply(resetPage: false);
        }
    }
}
