using CommunityToolkit.Mvvm.ComponentModel;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.ViewModels;

public partial class ReportDetailViewModel : ObservableObject
{
    [ObservableProperty]
    private Report? _selectedReport;
}
