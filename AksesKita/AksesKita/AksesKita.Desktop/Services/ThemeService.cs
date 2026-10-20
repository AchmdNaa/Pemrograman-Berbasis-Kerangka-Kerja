using System;
using System.IO;
using System.Windows;
using System.Windows.Media;
using AksesKita.Desktop.Rules;

namespace AksesKita.Desktop.Services;

/// <summary>
/// Mengatur tema terang/gelap. Setiap warna di <see cref="ThemePalette"/> dipasang sebagai brush
/// di Application.Resources; tampilan memakai {DynamicResource}, sehingga warna berganti saat itu juga.
/// Pilihan tema disimpan di theme.txt di samping aplikasi dan dipakai lagi saat dibuka berikutnya.
/// </summary>
public static class ThemeService
{
    private static readonly string SettingPath =
        Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "theme.txt");

    public static bool IsDark { get; private set; }

    public static event EventHandler? ThemeChanged;

    /// <summary>Dipanggil sekali saat aplikasi mulai, sebelum jendela dibuat.</summary>
    public static void Initialize()
    {
        IsDark = LoadSetting();
        Apply();
    }

    public static void SetDark(bool dark)
    {
        if (IsDark == dark) return;
        IsDark = dark;
        Apply();
        SaveSetting();
        ThemeChanged?.Invoke(null, EventArgs.Empty);
    }

    /// <summary>Brush sesuai tema yang sedang aktif; dipakai converter yang membuat warna lewat kode.</summary>
    public static Brush GetBrush(string key) =>
        Application.Current?.TryFindResource(key) as Brush ?? Brushes.Gray;

    private static void Apply()
    {
        var resources = Application.Current.Resources;
        foreach (var key in ThemePalette.Colors.Keys)
        {
            var color = (Color)ColorConverter.ConvertFromString("#" + ThemePalette.Hex(key, IsDark));
            var brush = new SolidColorBrush(color);
            brush.Freeze();
            // Brush baru dipasang (bukan diubah) karena brush lama bisa saja sudah beku.
            resources[key] = brush;
        }
    }

    private static bool LoadSetting()
    {
        try
        {
            return File.Exists(SettingPath) &&
                   File.ReadAllText(SettingPath).Trim().Equals("dark", StringComparison.OrdinalIgnoreCase);
        }
        catch (Exception)
        {
            return false; // gagal membaca: pakai tema terang
        }
    }

    private static void SaveSetting()
    {
        try
        {
            File.WriteAllText(SettingPath, IsDark ? "dark" : "light");
        }
        catch (Exception)
        {
            // Folder tidak bisa ditulis: tema tetap berlaku, hanya tidak diingat.
        }
    }
}
