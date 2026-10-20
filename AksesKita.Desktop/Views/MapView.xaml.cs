using System;
using System.IO;
using System.Windows;
using System.Windows.Controls;
using AksesKita.Desktop.ViewModels;

namespace AksesKita.Desktop.Views;

public partial class MapView : UserControl
{
    public MapView()
    {
        InitializeComponent();
        Loaded += OnLoaded;
    }

    private async void OnLoaded(object sender, RoutedEventArgs e)
    {
        try
        {
            await mapWebView.EnsureCoreWebView2Async();

            var path = Path.Combine(AppContext.BaseDirectory, "Assets", "Map", "index.html");

            // Setelah halaman selesai dimuat, kirim penanda laporan dari database ke Leaflet
            mapWebView.CoreWebView2.NavigationCompleted += async (_, _) =>
            {
                if (DataContext is MapViewModel vm)
                {
                    var json = await vm.GetMarkersJsonAsync();
                    await mapWebView.CoreWebView2.ExecuteScriptAsync($"setMarkers({json});");
                }
            };

            mapWebView.Source = new Uri(path);
        }
        catch (Exception)
        {
            MessageBox.Show(
                "Peta tidak dapat ditampilkan. Pastikan WebView2 Runtime sudah terpasang.",
                "AksesKita", MessageBoxButton.OK, MessageBoxImage.Warning);
        }
    }
}
