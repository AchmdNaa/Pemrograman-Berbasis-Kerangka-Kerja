using System;
using System.ComponentModel;
using System.Diagnostics;
using System.Globalization;
using System.IO;
using System.Windows;
using System.Windows.Controls;
using AksesKita.Desktop.ViewModels;

namespace AksesKita.Desktop.Views;

public partial class MapView : UserControl
{
    private MapViewModel? _vm;
    private bool _mapReady;

    public MapView()
    {
        InitializeComponent();
        Loaded += MapView_Loaded;
        Unloaded += MapView_Unloaded;
    }

    private async void MapView_Loaded(object sender, RoutedEventArgs e)
    {
        _vm = DataContext as MapViewModel;
        if (_vm != null)
        {
            _vm.PropertyChanged += Vm_PropertyChanged;
            _vm.FocusRequested += Vm_FocusRequested;
        }

        try
        {
            await mapWebView.EnsureCoreWebView2Async();
            mapWebView.CoreWebView2.WebMessageReceived += OnWebMessage;
            mapWebView.NavigationCompleted += async (_, _) =>
            {
                _mapReady = true;
                await PushPointsAsync();
                if (_vm?.PendingFocusId is int id)
                {
                    _vm.PendingFocusId = null;
                    await FocusAsync(id);
                }
            };

            var html = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "Assets", "Map", "index.html");
            if (File.Exists(html))
                mapWebView.Source = new Uri(html);
            else
                ShowFallback($"Berkas peta tidak ditemukan di:\n{html}");
        }
        catch (Exception ex)
        {
            ShowFallback("Peta membutuhkan WebView2 Runtime (Microsoft Edge). " +
                         "Pasang 'Evergreen WebView2 Runtime' lalu buka kembali menu ini.\n\n" + ex.Message);
        }
    }

    private void MapView_Unloaded(object sender, RoutedEventArgs e)
    {
        if (_vm != null)
        {
            _vm.PropertyChanged -= Vm_PropertyChanged;
            _vm.FocusRequested -= Vm_FocusRequested;
        }
    }

    private async void Vm_PropertyChanged(object? sender, PropertyChangedEventArgs e)
    {
        if (e.PropertyName == nameof(MapViewModel.PointsJson))
        {
            await PushPointsAsync();
            // A report may have been queued for focus while the data was still loading.
            if (_mapReady && _vm?.PendingFocusId is int id)
            {
                _vm.PendingFocusId = null;
                await FocusAsync(id);
            }
        }
    }

    private async void Vm_FocusRequested(int id) => await FocusAsync(id);

    private async System.Threading.Tasks.Task PushPointsAsync()
    {
        if (!_mapReady || _vm == null) return;
        await mapWebView.ExecuteScriptAsync($"setPoints({_vm.PointsJson});");
    }

    private async System.Threading.Tasks.Task FocusAsync(int id)
    {
        if (!_mapReady) return;
        await mapWebView.ExecuteScriptAsync($"focusPoint({id});");
    }

    /// <summary>Messages from the map popups look like "open:12" or "nav:12".</summary>
    private void OnWebMessage(object? sender, Microsoft.Web.WebView2.Core.CoreWebView2WebMessageReceivedEventArgs e)
    {
        var parts = e.TryGetWebMessageAsString()?.Split(':');
        if (parts is not { Length: 2 } || !int.TryParse(parts[1], out var id) || _vm == null) return;

        if (parts[0] == "open")
        {
            _vm.OpenReport(id);
        }
        else if (parts[0] == "nav")
        {
            var r = _vm.Find(id);
            if (r == null) return;
            var dest = $"{r.Latitude.ToString(CultureInfo.InvariantCulture)},{r.Longitude.ToString(CultureInfo.InvariantCulture)}";
            Process.Start(new ProcessStartInfo($"https://www.google.com/maps/dir/?api=1&destination={dest}&travelmode=walking")
            {
                UseShellExecute = true
            });
        }
    }

    private void ShowFallback(string message)
    {
        mapFallbackText.Text = message;
        mapFallback.Visibility = Visibility.Visible;
        mapWebView.Visibility = Visibility.Collapsed;
    }
}
