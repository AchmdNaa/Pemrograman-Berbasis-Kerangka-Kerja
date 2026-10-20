using System.Threading.Tasks;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Data;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class MainViewModel : ObservableObject
{
    [ObservableProperty]
    private ObservableObject _currentView;

    public DashboardViewModel DashboardVM { get; }
    public ReportListViewModel ReportListVM { get; }
    public CreateReportViewModel CreateReportVM { get; }
    public ReportDetailViewModel ReportDetailVM { get; }
    public MapViewModel MapVM { get; }

    // Dipakai oleh XAML (<vm:MainViewModel/>)
    public MainViewModel() : this(new ReportService(() => new AksesKitaDbContext()))
    {
    }

    public MainViewModel(IReportService service)
    {
        DashboardVM = new DashboardViewModel(service);
        ReportListVM = new ReportListViewModel(service);
        CreateReportVM = new CreateReportViewModel(service);
        ReportDetailVM = new ReportDetailViewModel(service);
        MapVM = new MapViewModel(service);

        // Dari daftar laporan, "Lihat Detail" membuka halaman detail
        ReportListVM.OpenDetailRequested = async report =>
        {
            await ReportDetailVM.LoadAsync(report.Id);
            CurrentView = ReportDetailVM;
        };

        // Setelah laporan tersimpan, pindah ke daftar laporan
        CreateReportVM.ReportSaved = async () =>
        {
            await ReportListVM.LoadAsync();
            CurrentView = ReportListVM;
        };

        // Setelah detail diubah atau dihapus, kembali ke daftar
        ReportDetailVM.BackRequested = async () =>
        {
            await ReportListVM.LoadAsync();
            CurrentView = ReportListVM;
        };

        _currentView = DashboardVM;
        _ = DashboardVM.LoadAsync();
    }

    [RelayCommand]
    private async Task Navigate(string destination)
    {
        switch (destination)
        {
            case "ReportList":
                await ReportListVM.LoadAsync();
                CurrentView = ReportListVM;
                break;
            case "CreateReport":
                await CreateReportVM.LoadAsync();
                CurrentView = CreateReportVM;
                break;
            case "ReportDetail":
                CurrentView = ReportDetailVM;
                break;
            case "Map":
                CurrentView = MapVM;
                break;
            default:
                await DashboardVM.LoadAsync();
                CurrentView = DashboardVM;
                break;
        }
    }
}
