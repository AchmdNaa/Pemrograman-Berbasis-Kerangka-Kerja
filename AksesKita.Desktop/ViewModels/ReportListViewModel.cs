using System;
using System.Collections.ObjectModel;
using System.Threading.Tasks;
using System.Windows;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class ReportListViewModel : ObservableObject
{
    private readonly IReportService _service;

    // Pilihan filter status; "Semua" berarti tanpa filter
    public string[] StatusOptions { get; } =
        { "Semua", "Baru", "Terverifikasi", "DalamPerbaikan", "Selesai", "Ditolak" };

    [ObservableProperty]
    private ObservableCollection<Report> _reports = new();

    [ObservableProperty]
    private string _searchKeyword = string.Empty;

    [ObservableProperty]
    private string _selectedStatusFilter = "Semua";

    [ObservableProperty]
    private Report? _selectedReport;

    [ObservableProperty]
    private string _message = string.Empty;

    // Diisi oleh MainViewModel untuk membuka halaman detail
    public Func<Report, Task>? OpenDetailRequested { get; set; }

    public ReportListViewModel(IReportService service)
    {
        _service = service;
    }

    // Pencarian dan filter langsung dijalankan saat pengguna mengetik / memilih
    partial void OnSearchKeywordChanged(string value) => _ = LoadAsync();
    partial void OnSelectedStatusFilterChanged(string value) => _ = LoadAsync();

    public async Task LoadAsync()
    {
        ReportStatus? status = Enum.TryParse<ReportStatus>(SelectedStatusFilter, out var parsed)
            ? parsed
            : null;

        var data = await _service.GetAllReportsAsync(SearchKeyword, status);
        Reports = new ObservableCollection<Report>(data);
        Message = data.Count == 0 ? "Tidak ada laporan yang cocok." : $"{data.Count} laporan ditemukan.";
    }

    [RelayCommand]
    private async Task OpenDetail()
    {
        if (SelectedReport == null)
        {
            Message = "Pilih salah satu laporan terlebih dahulu.";
            return;
        }

        if (OpenDetailRequested != null)
        {
            await OpenDetailRequested(SelectedReport);
        }
    }

    [RelayCommand]
    private async Task DeleteSelected()
    {
        if (SelectedReport == null)
        {
            Message = "Pilih salah satu laporan terlebih dahulu.";
            return;
        }

        var answer = MessageBox.Show(
            $"Hapus laporan {SelectedReport.TicketCode}? Tindakan ini tidak dapat dibatalkan.",
            "Konfirmasi Hapus",
            MessageBoxButton.YesNo,
            MessageBoxImage.Warning);

        if (answer != MessageBoxResult.Yes) return;

        await _service.DeleteReportAsync(SelectedReport.Id);
        await LoadAsync();
    }
}
