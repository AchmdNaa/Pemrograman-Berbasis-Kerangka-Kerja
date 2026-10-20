using System.ComponentModel;
using System.Windows;
using System.Windows.Media;
using AksesKita.Desktop.ViewModels;

namespace AksesKita.Desktop.Views;

public partial class MainWindow : Window
{
    public MainWindow()
    {
        InitializeComponent();
        DataContextChanged += (_, e) =>
        {
            if (e.NewValue is MainViewModel vm)
                vm.PropertyChanged += OnViewModelChanged;
        };
    }

    // The "Ukuran" button scales the whole app shell (accessibility option from the design).
    private void OnViewModelChanged(object? sender, PropertyChangedEventArgs e)
    {
        if (e.PropertyName == nameof(MainViewModel.UiScale) && sender is MainViewModel vm)
            ShellRoot.LayoutTransform = new ScaleTransform(vm.UiScale, vm.UiScale);
    }
}
