using CommunityToolkit.Mvvm.ComponentModel;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Services;

/// <summary>Holds the signed-in user for the current run (role-based, no password for the demo).</summary>
public partial class AppSession : ObservableObject
{
    [ObservableProperty] private string _userName = string.Empty;
    [ObservableProperty] private string _userEmail = string.Empty;
    [ObservableProperty] private UserRole _role = UserRole.Pelapor;

    public bool IsAdmin => Role == UserRole.Admin;
    public bool IsWarga => Role == UserRole.Pelapor;

    public string RoleLabel => Role == UserRole.Admin ? "Petugas Dinas PU Bina Marga" : "Warga Pelapor";

    partial void OnRoleChanged(UserRole value)
    {
        OnPropertyChanged(nameof(IsAdmin));
        OnPropertyChanged(nameof(IsWarga));
        OnPropertyChanged(nameof(RoleLabel));
    }
}

/// <summary>Navigation surface the child view-models use to move between pages.</summary>
public interface INavigator
{
    AppSession Session { get; }
    void Navigate(string page);
    void ShowReportDetail(int reportId);
    void ShowEditReport(int reportId);
    void ShowOnMap(int reportId);
}
