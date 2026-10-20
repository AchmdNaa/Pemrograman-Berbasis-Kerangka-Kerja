using System;
using System.Collections.ObjectModel;
using System.Globalization;
using System.Threading.Tasks;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class CreateReportViewModel : ObservableObject
{
    private readonly IReportService _service;

    public ObservableCollection<Category> Categories { get; } = new();
    public SeverityLevel[] SeverityOptions { get; } = Enum.GetValues<SeverityLevel>();

    [ObservableProperty]
    private string _title = string.Empty;

    [ObservableProperty]
    private string _description = string.Empty;

    [ObservableProperty]
    private string _address = string.Empty;

    // Koordinat diisi sebagai teks (contoh: -6.2088) lalu diubah ke angka saat disimpan
    [ObservableProperty]
    private string _latitudeText = "-6.2088";

    [ObservableProperty]
    private string _longitudeText = "106.8456";

    [ObservableProperty]
    private Category? _selectedCategory;

    [ObservableProperty]
    private SeverityLevel _severity = SeverityLevel.Rendah;

    [ObservableProperty]
    private string _reporterName = string.Empty;

    [ObservableProperty]
    private bool _isAnonymous;

    [ObservableProperty]
    private string _errorMessage = string.Empty;

    [ObservableProperty]
    private string _successMessage = string.Empty;

    // Diisi oleh MainViewModel: dipanggil setelah laporan tersimpan
    public Func<Task>? ReportSaved { get; set; }

    public CreateReportViewModel(IReportService service)
    {
        _service = service;
    }

    public async Task LoadAsync()
    {
        if (Categories.Count == 0)
        {
            foreach (var c in await _service.GetCategoriesAsync())
            {
                Categories.Add(c);
            }
            SelectedCategory = Categories.Count > 0 ? Categories[0] : null;
        }
    }

    [RelayCommand]
    private async Task SubmitReport()
    {
        ErrorMessage = string.Empty;
        SuccessMessage = string.Empty;

        if (SelectedCategory == null)
        {
            ErrorMessage = "Pilih kategori hambatan terlebih dahulu.";
            return;
        }

        if (!double.TryParse(LatitudeText, NumberStyles.Float, CultureInfo.InvariantCulture, out var lat)
            || !double.TryParse(LongitudeText, NumberStyles.Float, CultureInfo.InvariantCulture, out var lng))
        {
            ErrorMessage = "Koordinat harus berupa angka dengan titik desimal, contoh: -6.2088";
            return;
        }

        var report = new Report
        {
            Title = Title,
            Description = Description,
            Address = Address,
            Latitude = lat,
            Longitude = lng,
            CategoryId = SelectedCategory.Id,
            Severity = Severity,
            IsAnonymous = IsAnonymous,
            ReporterName = IsAnonymous || string.IsNullOrWhiteSpace(ReporterName) ? null : ReporterName.Trim()
        };

        try
        {
            await _service.AddReportAsync(report);
        }
        catch (InvalidOperationException ex)
        {
            // Pesan validasi dari service ditampilkan apa adanya; isian form tidak dihapus
            ErrorMessage = ex.Message;
            return;
        }
        catch (Exception)
        {
            ErrorMessage = "Laporan gagal disimpan. Coba lagi atau mulai ulang aplikasi.";
            return;
        }

        SuccessMessage = $"Laporan tersimpan dengan kode {report.TicketCode}.";
        Title = string.Empty;
        Description = string.Empty;
        Address = string.Empty;
        ReporterName = string.Empty;
        IsAnonymous = false;
        Severity = SeverityLevel.Rendah;

        if (ReportSaved != null)
        {
            await ReportSaved();
        }
    }
}
