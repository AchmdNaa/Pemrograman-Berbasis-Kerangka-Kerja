using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;

namespace AksesKita.Desktop.ViewModels;

public partial class MainViewModel : ObservableObject
{
    [ObservableProperty]
    private ObservableObject _currentView;

    public DashboardViewModel DashboardVM { get; } = new();
    public ReportListViewModel ReportListVM { get; } = new();
    public CreateReportViewModel CreateReportVM { get; } = new();
    public ReportDetailViewModel ReportDetailVM { get; } = new();
    public MapViewModel MapVM { get; } = new();

    public MainViewModel()
    {
        _currentView = DashboardVM;
    }

    [RelayCommand]
    private void Navigate(string destination)
    {
        CurrentView = destination switch
        {
            "Dashboard" => DashboardVM,
            "ReportList" => ReportListVM,
            "CreateReport" => CreateReportVM,
            "ReportDetail" => ReportDetailVM,
            "Map" => MapVM,
            _ => DashboardVM
        };
    }
}
