using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.ViewModels;

public partial class CreateReportViewModel : ObservableObject
{
    [ObservableProperty]
    private string _title = string.Empty;

    [ObservableProperty]
    private string _description = string.Empty;

    [ObservableProperty]
    private string _address = string.Empty;

    [ObservableProperty]
    private SeverityLevel _severity = SeverityLevel.Rendah;

    [ObservableProperty]
    private bool _isAnonymous;

    [RelayCommand]
    private void SubmitReport()
    {
        // Logika simpan laporan baru
    }
}
