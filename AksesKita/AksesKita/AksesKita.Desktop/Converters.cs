using System;
using System.Globalization;
using System.IO;
using System.Windows;
using System.Windows.Data;
using System.Windows.Media;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop;

/// <summary>True when the bound page key equals the ConverterParameter. Used to highlight the active sidebar item.</summary>
public class PageToBoolConverter : IValueConverter
{
    public object Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
        => string.Equals(value as string, parameter as string, StringComparison.Ordinal);

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}

/// <summary>Maps a severity level to an accent brush for chips and status dots (follows the active theme).</summary>
public class SeverityToBrushConverter : IValueConverter
{
    public object Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
        => ThemeService.GetBrush(value is SeverityLevel s
            ? s switch
            {
                SeverityLevel.Kritis => "DangerText",
                SeverityLevel.Sedang => "WarnText",
                _ => "OkText"
            }
            : "Muted");

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}

/// <summary>Light background tint that pairs with <see cref="SeverityToBrushConverter"/>; pass "border" for the matching outline.</summary>
public class SeverityToTintConverter : IValueConverter
{
    public object Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
    {
        var border = string.Equals(parameter as string, "border", StringComparison.OrdinalIgnoreCase);
        return ThemeService.GetBrush(value is SeverityLevel s
            ? s switch
            {
                SeverityLevel.Kritis => border ? "DangerBorder" : "DangerTint",
                SeverityLevel.Sedang => border ? "WarnBorder" : "WarnTint",
                _ => border ? "OkBorder" : "OkTint"
            }
            : border ? "Line" : "PageBg");
    }

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}

/// <summary>Foreground colour for a report status chip.</summary>
public class StatusToBrushConverter : IValueConverter
{
    public object Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
        => ThemeService.GetBrush(value is ReportStatus s
            ? s switch
            {
                ReportStatus.Baru => "Body2",
                ReportStatus.Terverifikasi => "InfoText",
                ReportStatus.DalamPerbaikan => "TealText",
                ReportStatus.Selesai => "OkText",
                _ => "RoseText"
            }
            : "Body2");

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}

/// <summary>Soft background for a report status chip; pass "border" for the matching outline.</summary>
public class StatusToTintConverter : IValueConverter
{
    public object Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
    {
        var border = string.Equals(parameter as string, "border", StringComparison.OrdinalIgnoreCase);
        return ThemeService.GetBrush(value is ReportStatus s
            ? s switch
            {
                ReportStatus.Baru => border ? "InputBorder" : "Hover",
                ReportStatus.Terverifikasi => border ? "InfoBorder" : "InfoTint",
                ReportStatus.DalamPerbaikan => border ? "TealBorder" : "TealTint",
                ReportStatus.Selesai => border ? "OkBorder" : "OkTint2",
                _ => border ? "DangerBorder" : "DangerTint2"
            }
            : border ? "InputBorder" : "Hover");
    }

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}

/// <summary>Picks the line icon for a facility category by its name (see Themes/Icons.xaml).</summary>
public class CategoryToIconConverter : IValueConverter
{
    public object? Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
    {
        var name = value as string ?? string.Empty;
        var key = name switch
        {
            _ when name.Contains("Trotoar", StringComparison.OrdinalIgnoreCase) => "IcoCatWalk",
            _ when name.Contains("Ramp", StringComparison.OrdinalIgnoreCase) => "IcoCatRamp",
            _ when name.Contains("Guiding", StringComparison.OrdinalIgnoreCase) => "IcoCatGuide",
            _ when name.Contains("Lift", StringComparison.OrdinalIgnoreCase) => "IcoCatLift",
            _ when name.Contains("Toilet", StringComparison.OrdinalIgnoreCase) => "IcoCatToilet",
            _ when name.Contains("Halte", StringComparison.OrdinalIgnoreCase) => "IcoCatBus",
            _ => "IcoDocument"
        };
        return Application.Current?.TryFindResource(key) as Geometry;
    }

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}

/// <summary>Turns an enum value into readable text ("DalamPerbaikan" → "Dalam Perbaikan").</summary>
public class EnumLabelConverter : IValueConverter
{
    public object Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Formatters.Label(value);

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}

/// <summary>Visible when the bound value is false (the built-in converter only does the opposite).</summary>
public class InverseBoolToVisibilityConverter : IValueConverter
{
    public object Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
        => value is true ? Visibility.Collapsed : Visibility.Visible;

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}

/// <summary>Visible when the bound string is an existing file; pass "invert" to flip it.</summary>
public class FileExistsToVisibilityConverter : IValueConverter
{
    public object Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
    {
        var exists = value is string path && !string.IsNullOrWhiteSpace(path) && File.Exists(path);
        if (string.Equals(parameter as string, "invert", StringComparison.OrdinalIgnoreCase)) exists = !exists;
        return exists ? Visibility.Visible : Visibility.Collapsed;
    }

    public object ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        => Binding.DoNothing;
}
