using System;
using System.Threading.Tasks;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class ReportDetailViewModel : ObservableObject
{
    private readonly IReportService _service;

    // Status yang boleh dipilih admin
    public ReportStatus[] StatusOptions { get; } = Enum.GetValues<ReportStatus>();

    [ObservableProperty]
    private Report? _selectedReport;

    [ObservableProperty]
    private ReportStatus _newStatus;

    [ObservableProperty]
    private string _statusNote = string.Empty;

    [ObservableProperty]
    private string _message = string.Empty;

    // Diisi oleh MainViewModel untuk kembali ke daftar laporan
    public Func<Task>? BackRequested { get; set; }

    public ReportDetailViewModel(IReportService service)
    {
        _service = service;
    }

    public async Task LoadAsync(int reportId)
    {
        Message = string.Empty;
        StatusNote = string.Empty;
        SelectedReport = await _service.GetReportByIdAsync(reportId);
        if (SelectedReport != null)
        {
            NewStatus = SelectedReport.Status;
        }
    }

    [RelayCommand]
    private async Task ChangeStatus()
    {
        if (SelectedReport == null) return;

        try
        {
            await _service.UpdateReportStatusAsync(SelectedReport.Id, NewStatus, StatusNote, "Admin");
        }
        catch (InvalidOperationException ex)
        {
            Message = ex.Message;
            return;
        }

        await LoadAsync(SelectedReport.Id);
        Message = "Status laporan diperbarui.";
    }

    [RelayCommand]
    private async Task Back()
    {
        if (BackRequested != null)
        {
            await BackRequested();
        }
    }
}
