using CommunityToolkit.Mvvm.ComponentModel;

namespace AksesKita.Desktop.ViewModels;

public partial class MapViewModel : ObservableObject
{
    [ObservableProperty]
    private string _filterCategory = "Semua";
}
