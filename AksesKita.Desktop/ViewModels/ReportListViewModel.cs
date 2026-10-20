using System.Collections.ObjectModel;
using CommunityToolkit.Mvvm.ComponentModel;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.ViewModels;

public partial class ReportListViewModel : ObservableObject
{
    [ObservableProperty]
    private ObservableCollection<Report> _reports = new();

    [ObservableProperty]
    private string _searchKeyword = string.Empty;
}
