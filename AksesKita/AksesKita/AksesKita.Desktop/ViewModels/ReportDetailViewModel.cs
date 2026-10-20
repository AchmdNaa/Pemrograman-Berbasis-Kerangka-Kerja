using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Globalization;
using System.Linq;
using System.Threading.Tasks;
using System.Windows;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Rules;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public record TimelineItem(string Title, string Time, string Detail, string Actor, string Color);

public partial class ReportDetailViewModel : ObservableObject
{
    private static readonly HashSet<int> Supported = new();

    private readonly IReportService _service;
    private readonly INavigator _nav;

    [ObservableProperty,
     NotifyPropertyChangedFor(nameof(HasReport)), NotifyPropertyChangedFor(nameof(ReporterDisplay)),
     NotifyPropertyChangedFor(nameof(SlaText)), NotifyPropertyChangedFor(nameof(BannerTitle)),
     NotifyPropertyChangedFor(nameof(BannerSubtitle)), NotifyPropertyChangedFor(nameof(CreatedText)),
     NotifyPropertyChangedFor(nameof(CoordinateText))]
    private Report? _report;

    [ObservableProperty] private bool _isAdmin;
    [ObservableProperty] private bool _isWarga;
    [ObservableProperty] private ReportStatus _selectedStatus;
    [ObservableProperty] private string _note = string.Empty;
    [ObservableProperty] private string _statusMessage = string.Empty;

    public ObservableCollection<TimelineItem> Timeline { get; } = new();

    /// <summary>Hanya status saat ini dan status yang boleh dituju (lihat StatusTransitionRules).</summary>
    public ObservableCollection<ReportStatus> StatusOptions { get; } = new();

    [ObservableProperty] private bool _canEdit;
    [ObservableProperty] private string _statusRuleHint = string.Empty;

    public bool HasReport => Report != null;
    public string ReporterDisplay => Report == null ? string.Empty
        : Report.IsAnonymous ? "Anonim (Warga Peduli)" : (Report.ReporterName ?? "Warga");
    public string SlaText => Report == null ? string.Empty : $"SLA Target: {Formatters.SlaTarget(Report.Severity)}";
    public string CreatedText => Report?.CreatedAt.ToString("dd MMM yyyy, HH:mm 'WIB'", new CultureInfo("id-ID")) ?? string.Empty;
    public string CoordinateText => Report == null ? string.Empty
        : $"{Report.Latitude.ToString("0.000000", CultureInfo.InvariantCulture)}, {Report.Longitude.ToString("0.000000", CultureInfo.InvariantCulture)}";

    public string BannerTitle => Report?.Severity switch
    {
        SeverityLevel.Kritis => "Status Urgensi Kritis: Berbahaya bagi Pengguna Tongkat Putih & Kursi Roda",
        SeverityLevel.Sedang => "Urgensi Sedang: Hambatan Membutuhkan Pendampingan",
        SeverityLevel.Rendah => "Urgensi Rendah: Hambatan Masih Dapat Dilalui dengan Hati-hati",
        _ => string.Empty
    };

    public string BannerSubtitle => Report == null ? string.Empty : Report.Status switch
    {
        ReportStatus.Baru => "Laporan menunggu verifikasi petugas Dinas PU Bina Marga Kota Surabaya.",
        ReportStatus.Terverifikasi => "Laporan telah diverifikasi dan masuk antrean penanganan lapangan.",
        ReportStatus.DalamPerbaikan => "Petugas teknis sedang mengerjakan perbaikan di lokasi.",
        ReportStatus.Selesai => "Perbaikan telah selesai dan divalidasi di lapangan.",
        _ => "Laporan tidak dapat ditindaklanjuti."
    };

    public ReportDetailViewModel(IReportService service, INavigator nav)
    {
        _service = service;
        _nav = nav;
    }

    /// <summary>Loads a ticket; pass 0 to open the newest one.</summary>
    public async Task LoadAsync(int reportId)
    {
        IsAdmin = _nav.Session.IsAdmin;
        IsWarga = _nav.Session.IsWarga;
        StatusMessage = string.Empty;
        Note = string.Empty;

        if (reportId <= 0)
        {
            var all = await _service.GetAllReportsAsync();
            reportId = all.FirstOrDefault()?.Id ?? 0;
        }

        Report = reportId > 0 ? await _service.GetReportByIdAsync(reportId) : null;
        CanEdit = Report != null && ReportPermissions.CanEdit(
            _nav.Session.IsAdmin, _nav.Session.UserName, Report.ReporterName, Report.Status);
        BuildTimeline();
    }

    private void BuildTimeline()
    {
        Timeline.Clear();
        StatusOptions.Clear();
        if (Report == null) return;

        foreach (var s in StatusTransitionRules.SelectableFrom(Report.Status)) StatusOptions.Add(s);
        SelectedStatus = Report.Status;
        StatusRuleHint = StatusTransitionRules.IsFinal(Report.Status)
            ? "Status akhir: hanya catatan tambahan yang dapat disimpan."
            : "Berikutnya: " + string.Join(" atau ", StatusTransitionRules.AllowedNext(Report.Status).Select(StatusTransitionRules.Label)) + ".";
        foreach (var l in Report.StatusLogs.OrderByDescending(l => l.CreatedAt))
        {
            var changed = l.OldStatus != l.NewStatus;
            Timeline.Add(new TimelineItem(
                changed ? $"Status: {Formatters.Label(l.OldStatus)} → {Formatters.Label(l.NewStatus)}" : $"Catatan Petugas (status {Formatters.Label(l.NewStatus)})",
                l.CreatedAt.ToString("dd MMM yyyy, HH:mm", new CultureInfo("id-ID")),
                string.IsNullOrWhiteSpace(l.Note) ? "Status diperbarui oleh petugas." : l.Note,
                $"Petugas: {l.UpdatedBy}",
                Formatters.StatusColor(l.NewStatus)));
        }
        Timeline.Add(new TimelineItem(
            "Laporan Diterbitkan",
            Report.CreatedAt.ToString("dd MMM yyyy, HH:mm", new CultureInfo("id-ID")),
            "Tiket keluhan aksesibilitas dibuat melalui portal AksesKita.",
            $"Pelapor: {ReporterDisplay}",
            "#64748B"));
    }

    [RelayCommand]
    private async Task SaveStatus()
    {
        if (Report == null) return;
        var error = StatusTransitionRules.Validate(Report.Status, SelectedStatus, Note);
        if (error != null)
        {
            StatusMessage = "⚠ " + error;
            return;
        }
        try
        {
            await _service.UpdateReportStatusAsync(Report.Id, SelectedStatus, Note.Trim(), _nav.Session.UserName);
        }
        catch (System.InvalidOperationException ex)
        {
            StatusMessage = "⚠ " + ex.Message;
            return;
        }
        var id = Report.Id;
        await LoadAsync(id);
        StatusMessage = "✓ Status berhasil diperbarui dan tercatat di riwayat.";
    }

    [RelayCommand]
    private async Task AddSupport()
    {
        if (Report == null) return;
        if (!Supported.Add(Report.Id))
        {
            StatusMessage = "Anda sudah memberi dukungan pada laporan ini.";
            return;
        }
        await _service.AddSupportAsync(Report.Id);
        var id = Report.Id;
        await LoadAsync(id);
        StatusMessage = "✓ Terima kasih, dukungan Anda memperkuat prioritas laporan.";
    }

    [RelayCommand]
    private void CopyTicket()
    {
        if (Report == null) return;
        try
        {
            Clipboard.SetText(Report.TicketCode);
            StatusMessage = $"✓ ID tiket {Report.TicketCode} disalin ke clipboard.";
        }
        catch
        {
            StatusMessage = "Clipboard sedang digunakan aplikasi lain, coba lagi.";
        }
    }

    [RelayCommand]
    private void OpenMap()
    {
        if (Report != null) _nav.ShowOnMap(Report.Id);
    }

    [RelayCommand]
    private void Back() => _nav.Navigate("ReportList");

    [RelayCommand]
    private void Edit()
    {
        if (Report != null && CanEdit) _nav.ShowEditReport(Report.Id);
    }

    [RelayCommand]
    private async Task DeleteReport()
    {
        if (Report == null) return;
        var ok = MessageBox.Show(
            $"Hapus laporan \"{Report.Title}\"?", "Konfirmasi Hapus",
            MessageBoxButton.YesNo, MessageBoxImage.Warning);
        if (ok == MessageBoxResult.Yes)
        {
            await _service.DeleteReportAsync(Report.Id);
            Report = null;
            _nav.Navigate("ReportList");
        }
    }
}
