using CommunityToolkit.Mvvm.ComponentModel;

namespace AksesKita.Desktop.ViewModels;

public partial class DashboardViewModel : ObservableObject
{
    [ObservableProperty]
    private int _totalReports = 1420;

    [ObservableProperty]
    private int _inProgress = 182;

    [ObservableProperty]
    private int _resolved = 1185;

    [ObservableProperty]
    private int _urgentCount = 18;
}
